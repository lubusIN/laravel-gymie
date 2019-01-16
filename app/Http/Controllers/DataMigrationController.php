<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Member;
use App\Invoice;
use App\InvoiceDetail;

class DataMigrationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function migrate()
    {
        DB::beginTransaction();
        try {
            //From: InvoiceDetail->tax To: Invoice->tax
            $oldInvoiceDetailsTax = InvoiceDetail::where('item_name', '=', 'Taxes')->get();

            foreach ($oldInvoiceDetailsTax as $taxDetail) {
                Invoice::where('id', $taxDetail->invoice_id)->update(['tax' => $taxDetail->item_amount]);
            }

            //From: InvoiceDetail->admission To: Invoice->additional_fees
            $oldInvoiceDetailsAdmission = InvoiceDetail::where('item_name', '=', 'Admission')->get();

            foreach ($oldInvoiceDetailsAdmission as $admissionDetail) {
                Invoice::where('id', $admissionDetail->invoice_id)->update(['additional_fees' => $admissionDetail->item_amount]);
            }

            //Now delete the admission and tax rows from InvoiceDetails
            InvoiceDetail::where('item_name', '=', 'Admission')->delete();
            InvoiceDetail::where('item_name', '=', 'Taxes')->delete();

            //From: Member->plan_id To: InvoiceDetail->plan_id
            $invoices = Invoice::all();

            foreach ($invoices as $invoice) {
                InvoiceDetail::where('invoice_id', $invoice->id)->update(['plan_id' => $invoice->member->plan_id]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
        }
    }

    public function migrateMedia()
    {
        DB::beginTransaction();

        try {
            $members = Member::all();

            foreach ($members as $member) {
                $profileImage = base_path('public/assets/img/profile/profile_'.$member->id.'.jpg');
                $proofImage = base_path('public/assets/img/proof/proof_'.$member->id.'.jpg');

                if (file_exists($profileImage)) {
                    $member->addMedia($profileImage)->usingFileName('profile_'.$member->id.'.jpg')->toCollection('profile');
                }

                if (file_exists($proofImage)) {
                    $member->addMedia($proofImage)->usingFileName('proof_'.$member->id.'.jpg')->toCollection('proof');
                }

                DB::commit();
            }
        } catch (\Exception $e) {
            DB::rollback();
        }
    }
}
