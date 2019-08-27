<?php

namespace App\Http\Controllers;

use DB;
use JavaScript;
use App\Invoice;
use App\Service;
use Carbon\Carbon;
use App\ChequeDetail;
use App\Subscription;
use App\InvoiceDetail;
use App\PaymentDetail;
use Illuminate\Http\Request;

class InvoicesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $invoices = Invoice::indexQuery($request->sort_field, $request->sort_direction, $request->drp_start, $request->drp_end)->search('"'.$request->input('search').'"')->paginate(10);
        $count = $invoices->total();

        if (! $request->has('drp_start') or ! $request->has('drp_end')) {
            $drp_placeholder = 'Select daterange filter';
        } else {
            $drp_placeholder = $request->drp_start.' - '.$request->drp_end;
        }

        $request->flash();

        return view('invoices.index', compact('invoices', 'count', 'drp_placeholder'));
    }

    public function unpaid(Request $request)
    {
        $invoices = Invoice::indexQuery($request->sort_field, $request->sort_direction, $request->drp_start, $request->drp_end)->search('"'.$request->input('search').'"')->where('trn_invoice.status', 0)->paginate(10);
        $invoicesTotal = Invoice::indexQuery($request->sort_field, $request->sort_direction, $request->drp_start, $request->drp_end)->search('"'.$request->input('search').'"')->where('trn_invoice.status', 0)->get();
        $count = $invoicesTotal->count();

        if (! $request->has('drp_start') or ! $request->has('drp_end')) {
            $drp_placeholder = 'Select daterange filter';
        } else {
            $drp_placeholder = $request->drp_start.' - '.$request->drp_end;
        }

        $request->flash();

        return view('invoices.unpaid', compact('invoices', 'count', 'drp_placeholder'));
    }

    public function paid(Request $request)
    {
        $invoices = Invoice::indexQuery($request->sort_field, $request->sort_direction, $request->drp_start, $request->drp_end)->search('"'.$request->input('search').'"')->where('trn_invoice.status', 1)->paginate(10);
        $invoicesTotal = Invoice::indexQuery($request->sort_field, $request->sort_direction, $request->drp_start, $request->drp_end)->search('"'.$request->input('search').'"')->where('trn_invoice.status', 1)->get();
        $count = $invoicesTotal->count();

        if (! $request->has('drp_start') or ! $request->has('drp_end')) {
            $drp_placeholder = 'Select daterange filter';
        } else {
            $drp_placeholder = $request->drp_start.' - '.$request->drp_end;
        }

        $request->flash();

        return view('invoices.paid', compact('invoices', 'count', 'drp_placeholder'));
    }

    public function partial(Request $request)
    {
        $invoices = Invoice::indexQuery($request->sort_field, $request->sort_direction, $request->drp_start, $request->drp_end)->search('"'.$request->input('search').'"')->where('trn_invoice.status', 2)->paginate(10);
        $invoicesTotal = Invoice::indexQuery($request->sort_field, $request->sort_direction, $request->drp_start, $request->drp_end)->search('"'.$request->input('search').'"')->where('trn_invoice.status', 2)->get();
        $count = $invoicesTotal->count();

        if (! $request->has('drp_start') or ! $request->has('drp_end')) {
            $drp_placeholder = 'Select daterange filter';
        } else {
            $drp_placeholder = $request->drp_start.' - '.$request->drp_end;
        }

        $request->flash();

        return view('invoices.partial', compact('invoices', 'count', 'drp_placeholder'));
    }

    public function overpaid(Request $request)
    {
        $invoices = Invoice::indexQuery($request->sort_field, $request->sort_direction, $request->drp_start, $request->drp_end)->search('"'.$request->input('search').'"')->where('trn_invoice.status', 3)->paginate(10);
        $invoicesTotal = Invoice::indexQuery($request->sort_field, $request->sort_direction, $request->drp_start, $request->drp_end)->search('"'.$request->input('search').'"')->where('trn_invoice.status', 3)->get();
        $count = $invoicesTotal->count();

        if (! $request->has('drp_start') or ! $request->has('drp_end')) {
            $drp_placeholder = 'Select daterange filter';
        } else {
            $drp_placeholder = $request->drp_start.' - '.$request->drp_end;
        }

        $request->flash();

        return view('invoices.overpaid', compact('invoices', 'count', 'drp_placeholder'));
    }

    public function show($id)
    {
        $invoice = Invoice::findOrFail($id);
        $settings = \Utilities::getSettings();

        return view('invoices.show', compact('invoice', 'settings'));
    }

    public function createPayment($id, Request $request)
    {
        $invoice = Invoice::findOrFail($id);

        return view('payments.create', compact('invoice'));
    }

    public function delete($id)
    {
        DB::beginTransaction();

        try {
            InvoiceDetail::where('invoice_id', $id)->delete();
            $payment_details = PaymentDetail::where('invoice_id', $id)->get();

            foreach ($payment_details as $payment_detail) {
                ChequeDetail::where('payment_id', $payment_detail->id)->delete();
                $payment_detail->delete();
            }

            Subscription::where('invoice_id', $id)->delete();
            Invoice::destroy($id);

            DB::commit();

            return back();
        } catch (\Exception $e) {
            DB::rollback();

            return back();
        }
    }

    public function discount($id)
    {
        $invoice = Invoice::findOrFail($id);

        JavaScript::put([
            'taxes' => \Utilities::getSetting('taxes'),
            'gymieToday' => Carbon::today()->format('Y-m-d'),
            'servicesCount' => Service::count(),
        ]);

        return view('invoices.discount', compact('invoice'));
    }

    public function applyDiscount($id, Request $request)
    {
        DB::beginTransaction();

        try {
            $invoice_total = $request->admission_amount + $request->subscription_amount + $request->taxes_amount - $request->discount_amount;
            $already_paid = PaymentDetail::leftJoin('trn_cheque_details', 'trn_payment_details.id', '=', 'trn_cheque_details.payment_id')
                                       ->whereRaw("trn_payment_details.invoice_id = $id AND (trn_cheque_details.`status` = 2 or trn_cheque_details.`status` IS NULL)")
                                       ->sum('trn_payment_details.payment_amount');

            $pending = $invoice_total - $already_paid;

            $status = \Utilities::setInvoiceStatus($pending, $invoice_total);

            Invoice::where('id', $id)->update(['invoice_number'=> $request->invoice_number,
                                         'total'=> $invoice_total,
                                         'status'=> $status,
                                         'pending_amount'=> $pending,
                                         'discount_amount'=> $request->discount_amount,
                                         'discount_percent'=> $request->discount_percent,
                                         'discount_note'=> $request->discount_note,
                                         'tax'=> $request->taxes_amount,
                                         'additional_fees'=> $request->additional_fees,
                                         'note'=>' ', ]);

            DB::commit();
            flash()->success('Discount was successfully updated');

            return redirect(action('InvoicesController@show', ['id' => $id]));
        } catch (\Exception $e) {
            DB::rollback();
            flash()->error('Error while updating discount. Please try again');

            return back();
        }
    }
}
