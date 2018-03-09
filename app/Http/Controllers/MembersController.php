<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Member;
use JavaScript;
use App\Enquiry;
use App\Invoice;
use App\Service;
use App\Setting;
use Carbon\Carbon;
use App\Sms_trigger;
use App\Subscription;
use App\Cheque_detail;
use App\Invoice_detail;
use App\Payment_detail;
use Illuminate\Http\Request;

class MembersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $members = Member::indexQuery($request->sort_field, $request->sort_direction, $request->drp_start, $request->drp_end)->search('"'.$request->input('search').'"')->paginate(10);
        $memberTotal = Member::indexQuery($request->sort_field, $request->sort_direction, $request->drp_start, $request->drp_end)->search('"'.$request->input('search').'"')->get();
        $count = $memberTotal->count();

        if (! $request->has('drp_start') or ! $request->has('drp_end')) {
            $drp_placeholder = 'Select daterange filter';
        } else {
            $drp_placeholder = $request->drp_start.' - '.$request->drp_end;
        }

        $request->flash();

        return view('members.index', compact('members', 'count', 'drp_placeholder', 'old_sort'));
    }

    public function active(Request $request)
    {
        $members = Member::active($request->sort_field, $request->sort_direction, $request->drp_start, $request->drp_end)->search('"'.$request->input('search').'"')->paginate(10);
        $Totalmembers = Member::active($request->sort_field, $request->sort_direction, $request->drp_start, $request->drp_end)->search('"'.$request->input('search').'"')->get();
        $count = $Totalmembers->count();

        if (! $request->has('drp_start') or ! $request->has('drp_end')) {
            $drp_placeholder = 'Select daterange filter';
        } else {
            $drp_placeholder = $request->drp_start.' - '.$request->drp_end;
        }

        $request->flash();

        return view('members.active', compact('members', 'count', 'drp_placeholder', 'old_sort'));
    }

    public function inactive(Request $request)
    {
        $members = Member::inactive($request->sort_field, $request->sort_direction, $request->drp_start, $request->drp_end)->search('"'.$request->input('search').'"')->paginate(10);
        $Totalmembers = Member::inactive($request->sort_field, $request->sort_direction, $request->drp_start, $request->drp_end)->search('"'.$request->input('search').'"')->get();
        $count = $Totalmembers->count();

        if (! $request->has('drp_start') or ! $request->has('drp_end')) {
            $drp_placeholder = 'Select daterange filter';
        } else {
            $drp_placeholder = $request->drp_start.' - '.$request->drp_end;
        }

        $request->flash();

        return view('members.inactive', compact('members', 'count', 'drp_placeholder', 'old_sort'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $member = Member::findOrFail($id);

        return view('members.show', compact('member'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        // For Tax calculation
        JavaScript::put([
            'taxes' => \Utilities::getSetting('taxes'),
            'gymieToday' => Carbon::today()->format('Y-m-d'),
            'servicesCount' => Service::count(),
        ]);

        //Get Numbering mode
        $invoice_number_mode = \Utilities::getSetting('invoice_number_mode');
        $member_number_mode = \Utilities::getSetting('member_number_mode');

        //Generating Invoice number
        if ($invoice_number_mode == \constNumberingMode::Auto) {
            $invoiceCounter = \Utilities::getSetting('invoice_last_number') + 1;
            $invoicePrefix = \Utilities::getSetting('invoice_prefix');
            $invoice_number = $invoicePrefix.$invoiceCounter;
        } else {
            $invoice_number = '';
            $invoiceCounter = '';
        }

        //Generating Member Counter
        if ($member_number_mode == \constNumberingMode::Auto) {
            $memberCounter = \Utilities::getSetting('member_last_number') + 1;
            $memberPrefix = \Utilities::getSetting('member_prefix');
            $member_code = $memberPrefix.$memberCounter;
        } else {
            $member_code = '';
            $memberCounter = '';
        }

        return view('members.create', compact('invoice_number', 'invoiceCounter', 'member_code', 'memberCounter', 'member_number_mode', 'invoice_number_mode'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        // Member Model Validation
        $this->validate($request, ['email' => 'unique:mst_members,email',
                                   'contact' => 'unique:mst_members,contact',
                                   'member_code' => 'unique:mst_members,member_code', ]);

        // Start Transaction
        DB::beginTransaction();

        try {
            // Store member's personal details
            $memberData = ['name'=>$request->name,
                                    'DOB'=> $request->DOB,
                                    'gender'=> $request->gender,
                                    'contact'=> $request->contact,
                                    'emergency_contact'=> $request->emergency_contact,
                                    'health_issues'=> $request->health_issues,
                                    'email'=> $request->email,
                                    'address'=> $request->address,
                                    'member_id'=> $request->member_id,
                                    'proof_name'=> $request->proof_name,
                                    'member_code'=> $request->member_code,
                                    'status'=> $request->status,
                                    'pin_code'=> $request->pin_code,
                                    'occupation'=> $request->occupation,
                                    'aim'=> $request->aim,
                                    'source'=> $request->source, ];

            $member = new Member($memberData);
            $member->createdBy()->associate(Auth::user());
            $member->updatedBy()->associate(Auth::user());
            $member->save();

            // Adding media i.e. Profile & proof photo
            if ($request->hasFile('photo')) {
                $member->addMedia($request->file('photo'))->usingFileName('profile_'.$member->id.$request->photo->getClientOriginalExtension())->toCollection('profile');
            }

            if ($request->hasFile('proof_photo')) {
                $member->addMedia($request->file('proof_photo'))->usingFileName('proof_'.$member->id.$request->proof_photo->getClientOriginalExtension())->toCollection('proof');
            }

            // Helper function for calculating payment status
            $invoice_total = $request->admission_amount + $request->subscription_amount + $request->taxes_amount - $request->discount_amount;
            $paymentStatus = \constPaymentStatus::Unpaid;
            $pending = $invoice_total - $request->payment_amount;

            if ($request->mode == 1) {
                if ($request->payment_amount == $invoice_total) {
                    $paymentStatus = \constPaymentStatus::Paid;
                } elseif ($request->payment_amount > 0 && $request->payment_amount < $invoice_total) {
                    $paymentStatus = \constPaymentStatus::Partial;
                } elseif ($request->payment_amount == 0) {
                    $paymentStatus = \constPaymentStatus::Unpaid;
                } else {
                    $paymentStatus = \constPaymentStatus::Overpaid;
                }
            }

            // Storing Invoice
            $invoiceData = ['invoice_number'=> $request->invoice_number,
                                     'member_id'=> $member->id,
                                     'total'=> $invoice_total,
                                     'status'=> $paymentStatus,
                                     'pending_amount'=> $pending,
                                     'discount_amount'=> $request->discount_amount,
                                     'discount_percent'=> $request->discount_percent,
                                     'discount_note'=> $request->discount_note,
                                     'tax'=> $request->taxes_amount,
                                     'additional_fees'=> $request->additional_fees,
                                     'note'=>' ', ];

            $invoice = new Invoice($invoiceData);
            $invoice->createdBy()->associate(Auth::user());
            $invoice->updatedBy()->associate(Auth::user());
            $invoice->save();

            // Storing subscription
            foreach ($request->plan as $plan) {
                $subscriptionData = ['member_id'=> $member->id,
                                            'invoice_id'=> $invoice->id,
                                            'plan_id'=> $plan['id'],
                                            'start_date'=> $plan['start_date'],
                                            'end_date'=> $plan['end_date'],
                                            'status'=> \constSubscription::onGoing,
                                            'is_renewal'=>'0', ];

                $subscription = new Subscription($subscriptionData);
                $subscription->createdBy()->associate(Auth::user());
                $subscription->updatedBy()->associate(Auth::user());
                $subscription->save();

                //Adding subscription to invoice(Invoice Details)
                $detailsData = ['invoice_id'=> $invoice->id,
                                       'plan_id'=> $plan['id'],
                                       'item_amount'=> $plan['price'], ];

                $invoice_details = new Invoice_detail($detailsData);
                $invoice_details->createdBy()->associate(Auth::user());
                $invoice_details->updatedBy()->associate(Auth::user());
                $invoice_details->save();
            }

            // Store Payment Details
            $paymentData = ['invoice_id'=> $invoice->id,
                                     'payment_amount'=> $request->payment_amount,
                                     'mode'=> $request->mode,
                                     'note'=> ' ', ];

            $payment_details = new Payment_detail($paymentData);
            $payment_details->createdBy()->associate(Auth::user());
            $payment_details->updatedBy()->associate(Auth::user());
            $payment_details->save();

            if ($request->mode == 0) {
                // Store Cheque Details
                $chequeData = ['payment_id'=> $payment_details->id,
                                      'number'=> $request->number,
                                      'date'=> $request->date,
                                      'status'=> \constChequeStatus::Recieved, ];

                $cheque_details = new Cheque_detail($chequeData);
                $cheque_details->createdBy()->associate(Auth::user());
                $cheque_details->updatedBy()->associate(Auth::user());
                $cheque_details->save();
            }

            // On member transfer update enquiry Status
            if ($request->has('transfer_id')) {
                $enquiry = Enquiry::findOrFail($request->transfer_id);
                $enquiry->status = \constEnquiryStatus::Member;
                $enquiry->updatedBy()->associate(Auth::user());
                $enquiry->save();
            }

            //Updating Numbering Counters
            Setting::where('key', '=', 'invoice_last_number')->update(['value' => $request->invoiceCounter]);
            Setting::where('key', '=', 'member_last_number')->update(['value' => $request->memberCounter]);
            $sender_id = \Utilities::getSetting('sms_sender_id');
            $gym_name = \Utilities::getSetting('gym_name');

            //SMS Trigger
            if ($invoice->status == \constPaymentStatus::Paid) {
                $sms_trigger = Sms_trigger::where('alias', '=', 'member_admission_with_paid_invoice')->first();
                $message = $sms_trigger->message;
                $sms_text = sprintf($message, $member->name, $gym_name, $payment_details->payment_amount, $invoice->invoice_number);
                $sms_status = $sms_trigger->status;

                \Utilities::Sms($sender_id, $member->contact, $sms_text, $sms_status);
            } elseif ($invoice->status == \constPaymentStatus::Partial) {
                $sms_trigger = Sms_trigger::where('alias', '=', 'member_admission_with_partial_invoice')->first();
                $message = $sms_trigger->message;
                $sms_text = sprintf($message, $member->name, $gym_name, $payment_details->payment_amount, $invoice->invoice_number, $invoice->pending_amount);
                $sms_status = $sms_trigger->status;

                \Utilities::Sms($sender_id, $member->contact, $sms_text, $sms_status);
            } elseif ($invoice->status == \constPaymentStatus::Unpaid) {
                if ($request->mode == 0) {
                    $sms_trigger = Sms_trigger::where('alias', '=', 'payment_with_cheque')->first();
                    $message = $sms_trigger->message;
                    $sms_text = sprintf($message, $member->name, $payment_details->payment_amount, $cheque_details->number, $invoice->invoice_number, $gym_name);
                    $sms_status = $sms_trigger->status;

                    \Utilities::Sms($sender_id, $member->contact, $sms_text, $sms_status);
                } else {
                    $sms_trigger = Sms_trigger::where('alias', '=', 'member_admission_with_unpaid_invoice')->first();
                    $message = $sms_trigger->message;
                    $sms_text = sprintf($message, $member->name, $gym_name, $invoice->pending_amount, $invoice->invoice_number);
                    $sms_status = $sms_trigger->status;

                    \Utilities::Sms($sender_id, $member->contact, $sms_text, $sms_status);
                }
            }

            if ($subscription->start_date < $member->created_at) {
                $member->created_at = $subscription->start_date;
                $member->updated_at = $subscription->start_date;
                $member->save();

                $invoice->created_at = $subscription->start_date;
                $invoice->updated_at = $subscription->start_date;
                $invoice->save();

                foreach ($invoice->invoice_details as $invoice_detail) {
                    $invoice_detail->created_at = $subscription->start_date;
                    $invoice_detail->updated_at = $subscription->start_date;
                    $invoice_detail->save();
                }

                $payment_details->created_at = $subscription->start_date;
                $payment_details->updated_at = $subscription->start_date;
                $payment_details->save();

                $subscription->created_at = $subscription->start_date;
                $subscription->updated_at = $subscription->start_date;
                $subscription->save();
            }

            DB::commit();
            flash()->success('Member was successfully created');

            return redirect(action('MembersController@show', ['id' => $member->id]));
        } catch (\Exception $e) {
            DB::rollback();
            flash()->error('Error while creating the member');

            return redirect(action('MembersController@index'));
        }
    }

    //End of new Member

    // End of store method

    /**
     * Edit a created resource in storage.
     *
     * @return Response
     */
    public function edit($id)
    {
        $member = Member::findOrFail($id);
        $member_number_mode = \Utilities::getSetting('member_number_mode');
        $member_code = $member->member_code;

        return view('members.edit', compact('member', 'member_number_mode', 'member_code'));
    }

    /**
     * Update an edited resource in storage.
     *
     * @return Response
     */
    public function update($id, Request $request)
    {
        //dd($request->all());
        $member = Member::findOrFail($id);
        $member->update($request->all());

        if ($request->hasFile('photo')) {
            $member->clearMediaCollection('profile');
            $member->addMedia($request->file('photo'))->usingFileName('profile_'.$member->id.$request->photo->getClientOriginalExtension())->toCollection('profile');
        }

        if ($request->hasFile('proof_photo')) {
            $member->clearMediaCollection('proof');
            $member->addMedia($request->file('proof_photo'))->usingFileName('proof_'.$member->id.$request->proof_photo->getClientOriginalExtension())->toCollection('proof');
        }

        $member->updatedBy()->associate(Auth::user());
        $member->save();

        flash()->success('Member details were successfully updated');

        return redirect(action('MembersController@show', ['id' => $member->id]));
    }

    /**
     * Archive a resource in storage.
     *
     * @return Response
     */
    public function archive($id, Request $request)
    {
        Subscription::where('member_id', $id)->delete();

        $invoices = Invoice::where('member_id', $id)->get();

        foreach ($invoices as $invoice) {
            Invoice_detail::where('invoice_id', $invoice->id)->delete();
            $payment_details = Payment_detail::where('invoice_id', $invoice->id)->get();

            foreach ($payment_details as $payment_detail) {
                Cheque_detail::where('payment_id', $payment_detail->id)->delete();
                $payment_detail->delete();
            }

            $invoice->delete();
        }

        $member = Member::findOrFail($id);
        $member->clearMediaCollection('profile');
        $member->clearMediaCollection('proof');

        $member->delete();

        return back();
    }

    public function transfer($id, Request $request)
    {
        // For Tax calculation
        JavaScript::put([
            'taxes' => \Utilities::getSetting('taxes'),
            'gymieToday' => Carbon::today()->format('Y-m-d'),
            'servicesCount' => Service::count(),
        ]);

        //Get Numbering mode
        $invoice_number_mode = \Utilities::getSetting('invoice_number_mode');
        $member_number_mode = \Utilities::getSetting('member_number_mode');

        //Generating Invoice number
        if ($invoice_number_mode == \constNumberingMode::Auto) {
            $invoiceCounter = \Utilities::getSetting('invoice_last_number') + 1;
            $invoicePrefix = \Utilities::getSetting('invoice_prefix');
            $invoice_number = $invoicePrefix.$invoiceCounter;
        } else {
            $invoice_number = '';
            $invoiceCounter = '';
        }

        //Generating Member Counter
        if ($member_number_mode == \constNumberingMode::Auto) {
            $memberCounter = \Utilities::getSetting('member_last_number') + 1;
            $memberPrefix = \Utilities::getSetting('member_prefix');
            $member_code = $memberPrefix.$memberCounter;
        } else {
            $member_code = '';
            $memberCounter = '';
        }

        $enquiry = Enquiry::findOrFail($id);

        return view('members.transfer', compact('enquiry', 'invoice_number', 'invoiceCounter', 'member_code', 'memberCounter', 'member_number_mode', 'invoice_number_mode'));
    }
}
