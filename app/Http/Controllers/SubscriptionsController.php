<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use JavaScript;
use App\Invoice;
use App\Service;
use App\Setting;
use Carbon\Carbon;
use App\SmsTrigger;
use App\Subscription;
use App\ChequeDetail;
use App\InvoiceDetail;
use App\PaymentDetail;
use Illuminate\Http\Request;

class SubscriptionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $subscriptions = Subscription::indexQuery($request->sort_field, $request->sort_direction, $request->drp_start, $request->drp_end, $request->plan_name)->search('"'.$request->input('search').'"')->paginate(10);
        $subscriptionTotal = Subscription::indexQuery($request->sort_field, $request->sort_direction, $request->drp_start, $request->drp_end, $request->plan_name)->search('"'.$request->input('search').'"')->get();
        $count = $subscriptionTotal->count();

        if (! $request->has('drp_start') or ! $request->has('drp_end')) {
            $drp_placeholder = 'Select daterange filter';
        } else {
            $drp_placeholder = $request->drp_start.' - '.$request->drp_end;
        }

        $request->flash();

        return view('subscriptions.index', compact('subscriptions', 'count', 'drp_placeholder'));
    }

    public function expiring(Request $request)
    {
        $expirings = Subscription::expiring($request->sort_field, $request->sort_direction, $request->drp_start, $request->drp_end)->search('"'.$request->input('search').'"')->paginate(10);
        $expiringTotal = Subscription::expiring($request->sort_field, $request->sort_direction, $request->drp_start, $request->drp_end)->search('"'.$request->input('search').'"')->get();
        $count = $expiringTotal->count();

        if (! $request->has('drp_start') or ! $request->has('drp_end')) {
            $drp_placeholder = 'Select daterange filter';
        } else {
            $drp_placeholder = $request->drp_start.' - '.$request->drp_end;
        }

        $request->flash();

        return view('subscriptions.expiring', compact('expirings', 'count', 'drp_placeholder'));
    }

    public function expired(Request $request)
    {
        $allExpired = Subscription::expired($request->sort_field, $request->sort_direction, $request->drp_start, $request->drp_end)->search('"'.$request->input('search').'"')->paginate(10);
        $allExpiredTotal = Subscription::expired($request->sort_field, $request->sort_direction, $request->drp_start, $request->drp_end)->search('"'.$request->input('search').'"')->get();
        $count = $allExpiredTotal->count();

        if (! $request->has('drp_start') or ! $request->has('drp_end')) {
            $drp_placeholder = 'Select daterange filter';
        } else {
            $drp_placeholder = $request->drp_start.' - '.$request->drp_end;
        }

        $request->flash();

        return view('subscriptions.expired', compact('allExpired', 'count', 'drp_placeholder'));
    }

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

        //Generating Invoice number
        if ($invoice_number_mode == \constNumberingMode::Auto) {
            $invoiceCounter = \Utilities::getSetting('invoice_last_number') + 1;
            $invoicePrefix = \Utilities::getSetting('invoice_prefix');
            $invoice_number = $invoicePrefix.$invoiceCounter;
        } else {
            $invoice_number = '';
            $invoiceCounter = '';
        }

        return view('subscriptions.create', compact('invoice_number', 'invoiceCounter', 'invoice_number_mode'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            //Helper function to set Payment status
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
                                     'member_id'=> $request->member_id,
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
                $subscriptionData = ['member_id'=> $request->member_id,
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

                $invoice_details = new InvoiceDetail($detailsData);
                $invoice_details->createdBy()->associate(Auth::user());
                $invoice_details->updatedBy()->associate(Auth::user());
                $invoice_details->save();
            }

            //Payment Details
            $paymentData = ['invoice_id'=> $invoice->id,
                                   'payment_amount'=> $request->payment_amount,
                                   'mode'=> $request->mode,
                                   'note'=> ' ', ];

            $payment_details = new PaymentDetail($paymentData);
            $payment_details->createdBy()->associate(Auth::user());
            $payment_details->updatedBy()->associate(Auth::user());
            $payment_details->save();

            if ($request->mode == 0) {
                // Store Cheque Details
                $chequeData = ['payment_id'=> $payment_details->id,
                                    'number'=> $request->number,
                                    'date'=> $request->date,
                                    'status'=> \constChequeStatus::Recieved, ];

                $cheque_details = new ChequeDetail($chequeData);
                $cheque_details->createdBy()->associate(Auth::user());
                $cheque_details->updatedBy()->associate(Auth::user());
                $cheque_details->save();
            }

            // Set the subscription status of the 'Renewed' subscription to Renew
            if ($request->has('previousSubscriptions')) {
                Subscription::where('invoice_id', $invoice->id)->update(['is_renewal' => '1']);

                foreach ($request->previousSubscriptions as $subscriptionId) {
                    $oldSubscription = Subscription::findOrFail($subscriptionId);
                    $oldSubscription->status = \constSubscription::renewed;
                    $oldSubscription->updatedBy()->associate(Auth::user());
                    $oldSubscription->save();
                }
            }

            //Updating Numbering Counters
            Setting::where('key', '=', 'invoice_last_number')->update(['value' => $request->invoiceCounter]);
            $sender_id = \Utilities::getSetting('sms_sender_id');
            $gym_name = \Utilities::getSetting('gym_name');

            //SMS Trigger
            if ($invoice->status == \constPaymentStatus::Paid) {
                if ($request->mode == 0) {
                    $sms_trigger = SmsTrigger::where('alias', '=', 'payment_with_cheque')->first();
                    $message = $sms_trigger->message;
                    $sms_text = sprintf($message, $subscription->member->name, $payment_details->payment_amount, $cheque_details->number, $invoice->invoice_number, $gym_name);
                    $sms_status = $sms_trigger->status;

                    \Utilities::Sms($sender_id, $subscription->member->contact, $sms_text, $sms_status);
                } else {
                    $sms_trigger = SmsTrigger::where('alias', '=', 'subscription_renewal_with_paid_invoice')->first();
                    $message = $sms_trigger->message;
                    $sms_text = sprintf($message, $subscription->member->name, $payment_details->payment_amount, $invoice->invoice_number);
                    $sms_status = $sms_trigger->status;

                    \Utilities::Sms($sender_id, $subscription->member->contact, $sms_text, $sms_status);
                }
            } elseif ($invoice->status == \constPaymentStatus::Partial) {
                if ($request->mode == 0) {
                    $sms_trigger = SmsTrigger::where('alias', '=', 'payment_with_cheque')->first();
                    $message = $sms_trigger->message;
                    $sms_text = sprintf($message, $subscription->member->name, $payment_details->payment_amount, $cheque_details->number, $invoice->invoice_number, $gym_name);
                    $sms_status = $sms_trigger->status;

                    \Utilities::Sms($sender_id, $subscription->member->contact, $sms_text, $sms_status);
                } else {
                    $sms_trigger = SmsTrigger::where('alias', '=', 'subscription_renewal_with_partial_invoice')->first();
                    $message = $sms_trigger->message;
                    $sms_text = sprintf($message, $subscription->member->name, $payment_details->payment_amount, $invoice->invoice_number, $invoice->pending_amount);
                    $sms_status = $sms_trigger->status;

                    \Utilities::Sms($sender_id, $subscription->member->contact, $sms_text, $sms_status);
                }
            } elseif ($invoice->status == \constPaymentStatus::Unpaid) {
                if ($request->mode == 0) {
                    $sms_trigger = SmsTrigger::where('alias', '=', 'payment_with_cheque')->first();
                    $message = $sms_trigger->message;
                    $sms_text = sprintf($message, $subscription->member->name, $payment_details->payment_amount, $cheque_details->number, $invoice->invoice_number, $gym_name);
                    $sms_status = $sms_trigger->status;

                    \Utilities::Sms($sender_id, $subscription->member->contact, $sms_text, $sms_status);
                } else {
                    $sms_trigger = SmsTrigger::where('alias', '=', 'subscription_renewal_with_unpaid_invoice')->first();
                    $message = $sms_trigger->message;
                    $sms_text = sprintf($message, $subscription->member->name, $invoice->total, $invoice->invoice_number);
                    $sms_status = $sms_trigger->status;

                    \Utilities::Sms($sender_id, $subscription->member->contact, $sms_text, $sms_status);
                }
            }

            DB::commit();
            flash()->success('Subscription was successfully created');

            return redirect(action('SubscriptionsController@index'));
        } catch (\Exception $e) {
            DB::rollback();
            flash()->error('Error while creating the Subscription');

            return redirect(action('SubscriptionsController@index'));
        }
    }

    //End of store method

    public function edit($id)
    {
        $subscription = Subscription::findOrFail($id);
        // $carbonToday = Carbon::today()->format('Y-m-d');
        // $subscriptionEndDate = $subscription->end_date->format('Y-m-d');
        $diff = Carbon::today()->diffInDays($subscription->end_date);
        //$gymieDiff = $diff->format('Y-m-d');
        $gymieDiff = $subscription->end_date->addDays($diff);

        JavaScript::put([
          'gymieToday' => Carbon::today()->format('Y-m-d'),
          'gymieEndDate' => $subscription->end_date->format('Y-m-d'),
          'gymieDiff' => $gymieDiff->format('Y-m-d'),
      ]);

        return view('subscriptions.edit', compact('subscription'));
    }

    public function update($id, Request $request)
    {
        $subscription = Subscription::findOrFail($id);

        $subscription->update($request->all());
        $subscription->updatedBy()->associate(Auth::user());
        $subscription->save();
        flash()->success('Subscription details were successfully updated');

        return redirect('subscriptions/all');
    }

    public function renew($id, Request $request)
    {

        //Get Numbering mode
        $invoice_number_mode = \Utilities::getSetting('invoice_number_mode');

        //Generating Invoice number
        if ($invoice_number_mode == \constNumberingMode::Auto) {
            $invoiceCounter = \Utilities::getSetting('invoice_last_number') + 1;
            $invoicePrefix = \Utilities::getSetting('invoice_prefix');
            $invoice_number = $invoicePrefix.$invoiceCounter;
        } else {
            $invoice_number = '';
            $invoiceCounter = '';
        }

        $subscriptions = Subscription::where('invoice_id', $id)->get();
        $member_id = $subscriptions->pluck('member_id')->first();

        // Javascript Variables
        JavaScript::put([
            'taxes' => \Utilities::getSetting('taxes'),
            'gymieToday' => Carbon::today()->format('Y-m-d'),
            'servicesCount' => Service::count(),
            'currentServices' => $subscriptions->count(),
        ]);

        return view('subscriptions.renew', compact('subscriptions', 'member_id', 'invoice_number', 'invoiceCounter', 'invoice_number_mode'));
    }

    public function cancelSubscription($id)
    {
        DB::beginTransaction();
        try {
            $subscription = Subscription::findOrFail($id);

            $subscription->update(['status' => \constSubscription::cancelled]);

            $subscription->member->update(['status' => \constStatus::InActive]);

            DB::commit();
            flash()->success('Subscription was successfully cancelled');

            return redirect('subscriptions/expired');
        } catch (Exception $e) {
            DB::rollback();
            flash()->error('Error while cancelling the Subscription');

            return redirect('subscriptions/expired');
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();

        try {
            $subscription = Subscription::findOrFail($id);
            $invoice = Invoice::where('id', $subscription->invoice_id)->first();
            $invoice_details = InvoiceDetail::where('invoice_id', $invoice->id)->get();
            $payment_details = PaymentDetail::where('invoice_id', $invoice->id)->get();

            foreach ($invoice_details as $invoice_detail) {
                $invoice_detail->delete();
            }

            foreach ($payment_details as $payment_detail) {
                ChequeDetail::where('payment_id', $payment_detail->id)->delete();
                $payment_detail->delete();
            }

            $subscription->delete();
            $invoice->delete();

            DB::commit();

            return back();
        } catch (\Exception $e) {
            DB::rollback();

            return back();
        }
    }

    public function change($id)
    {
        $subscription = Subscription::findOrFail($id);

        $already_paid = PaymentDetail::leftJoin('trn_cheque_details', 'trn_payment_details.id', '=', 'trn_cheque_details.payment_id')
                                     ->whereRaw("trn_payment_details.invoice_id = $subscription->invoice_id AND (trn_cheque_details.`status` = 2 or trn_cheque_details.`status` IS NULL)")
                                     ->sum('trn_payment_details.payment_amount');

        JavaScript::put([
          'taxes' => \Utilities::getSetting('taxes'),
          'gymieToday' => Carbon::today()->format('Y-m-d'),
          'servicesCount' => Service::count(),
      ]);

        return view('subscriptions.change', compact('subscription', 'already_paid'));
    }

    public function modify($id, Request $request)
    {
        $subscription = Subscription::findOrFail($id);

        try {
            DB::beginTransaction();
            //Helper function to set Payment status
            $invoice_total = $request->admission_amount + $request->subscription_amount + $request->taxes_amount - $request->discount_amount;
            $paymentStatus = \constPaymentStatus::Unpaid;
            $total_paid = $request->payment_amount + $request->previous_payment;
            $pending = $invoice_total - $total_paid;

            if ($request->mode == 1) {
                if ($total_paid == $invoice_total) {
                    $paymentStatus = \constPaymentStatus::Paid;
                } elseif ($total_paid > 0 && $total_paid < $invoice_total) {
                    $paymentStatus = \constPaymentStatus::Partial;
                } elseif ($total_paid == 0) {
                    $paymentStatus = \constPaymentStatus::Unpaid;
                } else {
                    $paymentStatus = \constPaymentStatus::Overpaid;
                }
            }

            Invoice::where('id', $subscription->invoice_id)->update(['invoice_number'=> $request->invoice_number,
                                                               'total'=> $invoice_total,
                                                               'status'=> $paymentStatus,
                                                               'pending_amount'=> $pending,
                                                               'discount_amount'=> $request->discount_amount,
                                                               'discount_percent'=> $request->discount_percent,
                                                               'discount_note'=> $request->discount_note,
                                                               'tax'=> $request->taxes_amount,
                                                               'additional_fees'=> $request->additional_fees,
                                                               'note'=>' ', ]);

            foreach ($request->plan as $plan) {
                $subscription->update(['plan_id'=> $plan['id'],
                                        'start_date'=> $plan['start_date'],
                                        'end_date'=> $plan['end_date'],
                                        'status'=> \constSubscription::onGoing,
                                        'is_renewal'=>'0', ]);

                //Adding subscription to invoice(Invoice Details)

                InvoiceDetail::where('invoice_id', $subscription->invoice_id)->update(['plan_id'=> $plan['id'],
                                                                                         'item_amount'=> $plan['price'], ]);
            }

            //Payment Details
            $paymentData = ['invoice_id'=> $subscription->invoice_id,
                                   'payment_amount'=> $request->payment_amount,
                                   'mode'=> $request->mode,
                                   'note'=> ' ', ];

            $payment_details = new PaymentDetail($paymentData);
            $payment_details->createdBy()->associate(Auth::user());
            $payment_details->updatedBy()->associate(Auth::user());
            $payment_details->save();

            if ($request->mode == 0) {
                // Store Cheque Details
                $chequeData = ['payment_id'=> $payment_details->id,
                                    'number'=> $request->number,
                                    'date'=> $request->date,
                                    'status'=> \constChequeStatus::Recieved, ];

                $cheque_details = new ChequeDetail($chequeData);
                $cheque_details->createdBy()->associate(Auth::user());
                $cheque_details->updatedBy()->associate(Auth::user());
                $cheque_details->save();
            }

            DB::commit();
            flash()->success('Subscription was successfully changed');

            return redirect(action('MembersController@show', ['id' => $subscription->member_id]));
        } catch (\Exception $e) {
            DB::rollback();
            flash()->error('Error while changing the Subscription');

            return back();
        }
    }
}
