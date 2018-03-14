<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Invoice;
use App\SmsTrigger;
use App\ChequeDetail;
use App\PaymentDetail;
use Illuminate\Http\Request;

class PaymentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $payment_details = PaymentDetail::indexQuery($request->sort_field, $request->sort_direction, $request->drp_start, $request->drp_end)->search('"'.$request->input('search').'"')->paginate(10);
        $paymentTotal = PaymentDetail::indexQuery($request->sort_field, $request->sort_direction, $request->drp_start, $request->drp_end)->search('"'.$request->input('search').'"')->get();
        $count = $paymentTotal->sum('payment_amount');

        if (! $request->has('drp_start') or ! $request->has('drp_end')) {
            $drp_placeholder = 'Select daterange filter';
        } else {
            $drp_placeholder = $request->drp_start.' - '.$request->drp_end;
        }

        $request->flash();

        return view('payments.index', compact('payment_details', 'count', 'drp_placeholder'));
    }

    public function create()
    {
        return view('payments.create');
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            // Storing Payment Details
            $payment_detail = new PaymentDetail($request->all());
            $payment_detail->createdBy()->associate(Auth::user());
            $payment_detail->updatedBy()->associate(Auth::user());
            $payment_detail->save();

            if ($request->mode == \constPaymentMode::Cheque) {
                // Store Cheque Details
                $chequeData = ['payment_id'=> $payment_detail->id,
                                  'number'=> $request->number,
                                  'date'=> $request->date,
                                  'status'=> \constChequeStatus::Recieved, ];

                $cheque_details = new ChequeDetail($chequeData);
                $cheque_details->createdBy()->associate(Auth::user());
                $cheque_details->updatedBy()->associate(Auth::user());
                $cheque_details->save();
            } elseif ($request->mode == \constPaymentMode::Cash) {
                // Updating Invoice Status and amounts
                $invoice_total = $payment_detail->invoice->total;
                $payment_total = PaymentDetail::where('invoice_id', $payment_detail->invoice_id)->sum('payment_amount');
                $amount_due = $invoice_total - $payment_total;

                $payment_detail->invoice->pending_amount = $amount_due;
                $payment_detail->invoice->status = \Utilities::setInvoiceStatus($amount_due, $invoice_total);
                $payment_detail->invoice->save();
            }

            //If cheque reissued , set the status of the previous cheque detail to Reissued
            if ($request->has('previousPayment')) {
                $cheque_detail = ChequeDetail::where('payment_id', $request->previousPayment)->first();
                $cheque_detail->status = \constChequeStatus::Reissued;
                $cheque_detail->save();
            }

            // SMS Trigger
            $sender_id = \Utilities::getSetting('sms_sender_id');
            $gym_name = \Utilities::getSetting('gym_name');

            if ($request->mode == \constPaymentMode::Cash) {
                $sms_trigger = SmsTrigger::where('alias', '=', 'payment_recieved')->first();
                $message = $sms_trigger->message;
                $sms_text = sprintf($message, $payment_detail->invoice->member->name, $payment_detail->payment_amount, $payment_detail->invoice->invoice_number);
                $sms_status = $sms_trigger->status;
                $sender_id = \Utilities::getSetting('sms_sender_id');

                \Utilities::Sms($sender_id, $payment_detail->invoice->member->contact, $sms_text, $sms_status);
            } elseif ($request->mode == \constPaymentMode::Cheque) {
                $sms_trigger = SmsTrigger::where('alias', '=', 'payment_with_cheque')->first();
                $message = $sms_trigger->message;
                $sms_text = sprintf($message, $payment_detail->invoice->member->name, $payment_detail->payment_amount, $cheque_details->number, $payment_detail->invoice->invoice_number, $gym_name);
                $sms_status = $sms_trigger->status;

                \Utilities::Sms($sender_id, $payment_detail->invoice->member->contact, $sms_text, $sms_status);
            }

            DB::commit();
            flash()->success('Payment Details were successfully stored');

            return redirect(action('InvoicesController@show', ['id' => $payment_detail->invoice_id]));
        } catch (Exception $e) {
            DB::rollback();
            flash()->error('Payment Details weren\'t stored succesfully');

            return redirect('payments/all');
        }
    }

    public function edit($id)
    {
        $payment_detail = PaymentDetail::findOrFail($id);
        $cheque_detail = ChequeDetail::where('payment_id', $id)->first();

        return view('payments.edit', compact('payment_detail', 'cheque_detail'));
    }

    public function update($id, Request $request)
    {
        DB::beginTransaction();

        try {
            // Storing Payment Details
            $payment_detail = PaymentDetail::findOrFail($id);
            $payment_detail->update($request->all());
            $payment_detail->updatedBy()->associate(Auth::user());
            $payment_detail->save();

            if ($request->mode == \constPaymentMode::Cheque) {
                // Store Cheque Details
                $cheque_detail = ChequeDetail::where('payment_id', $id)->first();
                $cheque_detail->update(['number' => $request->number,
                                      'date' => $request->date,
                                    ]);
                $cheque_detail->updatedBy()->associate(Auth::user());
                $cheque_detail->save();
            } elseif ($request->mode == \constPaymentMode::Cash) {
                // Updating Invoice Status and amounts
                $invoice_total = $payment_detail->invoice->total;
                $payment_total = PaymentDetail::where('invoice_id', $payment_detail->invoice_id)->sum('payment_amount');
                $amount_due = $invoice_total - $payment_total;

                $payment_detail->invoice->pending_amount = $amount_due;
                $payment_detail->invoice->status = \Utilities::setInvoiceStatus($amount_due, $invoice_total);
                $payment_detail->invoice->updatedBy()->associate(Auth::user());
                $payment_detail->invoice->save();
            }

            DB::commit();
            flash()->success('Payment Details were successfully updated');

            return redirect(action('InvoicesController@show', ['id' => $payment_detail->invoice_id]));
        } catch (Exception $e) {
            DB::rollback();
            flash()->error('Payment Details weren\'t updated succesfully');

            return redirect('payments');
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();

        try {
            $payment_detail = PaymentDetail::findOrFail($id);
            $invoice = Invoice::where('id', $payment_detail->invoice_id)->first();
            $cheque_details = ChequeDetail::where('payment_id', $payment_detail->id)->get();

            foreach ($cheque_details as $cheque_detail) {
                $cheque_detail->delete();
            }

            $payment_detail->delete();

            $invoice_total = $invoice->total;
            $payment_total = PaymentDetail::leftJoin('trn_cheque_details', 'trn_payment_details.id', '=', 'trn_cheque_details.payment_id')
                                           ->whereRaw("trn_payment_details.invoice_id = $invoice->id AND (trn_cheque_details.`status` = 2 or trn_cheque_details.`status` IS NULL)")
                                           ->sum('trn_payment_details.payment_amount');

            $amount_due = $invoice_total - $payment_total;

            $invoice->pending_amount = $amount_due;
            $invoice->status = \Utilities::setInvoiceStatus($amount_due, $invoice_total);
            $invoice->updatedBy()->associate(Auth::user());
            $invoice->save();

            DB::commit();

            return redirect('payments/all');
        } catch (\Exception $e) {
            DB::rollback();

            return redirect('payments/all');
        }
    }

    public function depositCheque($id)
    {
        ChequeDetail::where('payment_id', $id)->update(['status' => \constChequeStatus::Deposited]);

        flash()->success('Cheque has been marked as deposited');

        return back();
    }

    public function clearCheque($id)
    {
        DB::beginTransaction();
        try {
            $payment_detail = PaymentDetail::findOrFail($id);

            // Updating cheque status
            $cheque_detail = ChequeDetail::where('payment_id', $id)->first();
            $cheque_detail->status = \constChequeStatus::Cleared;
            $cheque_detail->updatedBy()->associate(Auth::user());
            $cheque_detail->save();

            // Updating Invoice Status and amounts
            $invoice_total = $payment_detail->invoice->total;

            $payment_total = PaymentDetail::leftJoin('trn_cheque_details', 'trn_payment_details.id', '=', 'trn_cheque_details.payment_id')
                                           ->whereRaw("trn_payment_details.invoice_id = $payment_detail->invoice_id AND (trn_cheque_details.`status` = 2 or trn_cheque_details.`status` IS NULL)")
                                           ->sum('trn_payment_details.payment_amount');

            $amount_due = $invoice_total - $payment_total;

            $payment_detail->invoice->pending_amount = $amount_due;
            $payment_detail->invoice->status = \Utilities::setInvoiceStatus($amount_due, $invoice_total);
            $payment_detail->invoice->save();

            DB::commit();
            flash()->success('Cheque has been marked as cleared');

            return back();
        } catch (Exception $e) {
            DB::rollback();
            flash()->error('Error while marking the cheque as cleared');

            return back();
        }
    }

    public function chequeBounce($id)
    {
        ChequeDetail::where('payment_id', $id)->update(['status' => \constChequeStatus::Bounced]);

        flash()->success('Cheque has been marked as bounced');

        return back();
    }

    public function chequeReissue($id)
    {
        $payment_detail = PaymentDetail::findOrFail($id);

        return view('payments.reissue', compact('payment_detail'));
    }
}
