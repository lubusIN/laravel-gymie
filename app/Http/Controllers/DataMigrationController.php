<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use Excel;
use App\Plan;
use App\Member;
use App\Invoice;
use App\Setting;
use Carbon\Carbon;
use App\Subscription;
use App\Invoice_detail;
use App\Payment_detail;

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
            //From: Invoice_detail->tax To: Invoice->tax
            $oldInvoiceDetailsTax = Invoice_detail::where('item_name', '=', 'Taxes')->get();

            foreach ($oldInvoiceDetailsTax as $taxDetail) {
                Invoice::where('id', $taxDetail->invoice_id)->update(['tax' => $taxDetail->item_amount]);
            }

            //From: Invoice_detail->admission To: Invoice->additional_fees
            $oldInvoiceDetailsAdmission = Invoice_detail::where('item_name', '=', 'Admission')->get();

            foreach ($oldInvoiceDetailsAdmission as $admissionDetail) {
                Invoice::where('id', $admissionDetail->invoice_id)->update(['additional_fees' => $admissionDetail->item_amount]);
            }

            //Now delete the admission and tax rows from Invoice_details
            Invoice_detail::where('item_name', '=', 'Admission')->delete();
            Invoice_detail::where('item_name', '=', 'Taxes')->delete();

            //From: Member->plan_id To: Invoice_detail->plan_id
            $invoices = Invoice::all();

            foreach ($invoices as $invoice) {
                Invoice_detail::where('invoice_id', $invoice->id)->update(['plan_id' => $invoice->member->plan_id]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
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
            dd($e);
        }
    }

    public function migrateExcel()
    {
        $tax_percent = \Utilities::getSetting('taxes');

        $lines = Excel::load('import.csv')->get();

        DB::beginTransaction();

        try {
            foreach ($lines as $line) {
                $invoiceCounter = \Utilities::getSetting('invoice_last_number') + 1;
                $invoicePrefix = \Utilities::getSetting('invoice_prefix');
                $invoice_number = $invoicePrefix.$invoiceCounter;

                $memberCounter = \Utilities::getSetting('member_last_number') + 1;
                $memberPrefix = \Utilities::getSetting('member_prefix');
                $member_code = $memberPrefix.$memberCounter;

                $dob = Carbon::createFromFormat('d/m/Y', $line->get('dob'))->toDateString();
                $gender = ($line->get('gender') == 'male' ? 'm' : 'f');
                // $explodedName = explode(" ", $line->get('name'));
                $email = $member_code.'@evolvegym.in';

                $address = (empty($line->get('address')) ? 'Naigaon east' : $line->get('address'));

                $member = Member::create(['name'=> $line->get('name'),
                                        'DOB'=> $dob,
                                        'gender'=> $gender,
                                        'contact'=> $line->get('contact'),
                                        'emergency_contact'=> $line->get('emergency_contact'),
                                        'health_issues'=> $line->get('health_issues'),
                                        'email'=> $email,
                                        'address'=> $address,
                                        'proof_name'=> 'none',
                                        'member_code'=> $member_code,
                                        'status'=> '1',
                                        'pin_code'=> 401208,
                                        'occupation'=> '5',
                                        'aim'=> '0',
                                        'source'=> '0',
                                        'created_by'=> Auth::user()->id,
                                        'updated_by'=> Auth::user()->id,
                                    ]);

                $invoice_total = $line->get('total_amount');
                $paymentStatus = \constPaymentStatus::Unpaid;
                $pending = empty($line->get('pending_amount')) ? '0' : $line->get('pending_amount');
                $discount = empty($line->get('discount_amount')) ? '0' : $line->get('discount_amount');
                $payment_amount = $invoice_total - (int) $pending;

                if ($payment_amount == $invoice_total) {
                    $paymentStatus = \constPaymentStatus::Paid;
                } elseif ($payment_amount > 0 && $payment_amount < $invoice_total) {
                    $paymentStatus = \constPaymentStatus::Partial;
                } elseif ($payment_amount == 0) {
                    $paymentStatus = \constPaymentStatus::Unpaid;
                } else {
                    $paymentStatus = \constPaymentStatus::Overpaid;
                }

                if (empty($line->discount_percent) && ! empty($line->discount_amount)) {
                    $discountPercent = 'custom';
                } elseif (empty($line->discount_percent) && empty($line->discount_amount)) {
                    $discountPercent = '0';
                } elseif (! empty($line->discount_percent)) {
                    $discountPercent = str_replace('%', '', $line->discount_percent);
                }

                $invoice = Invoice::create(['invoice_number'=> $invoice_number,
                                             'member_id'=> $member->id,
                                             'total'=> $invoice_total,
                                             'status'=> $paymentStatus,
                                             'pending_amount'=> $pending,
                                             'discount_amount'=> $discount,
                                             'discount_percent'=> $discountPercent,
                                             'discount_note'=> null,
                                             'tax'=> ($tax_percent / 100) * $invoice_total,
                                             'additional_fees'=> '0',
                                             'note'=> null,
                                             'created_by'=> Auth::user()->id,
                                             'updated_by'=> Auth::user()->id,
                                            ]);

                $start_date = Carbon::createFromFormat('d/m/Y', $line->get('start_date'));
                $planId = $line->get('plan');

                if ($planId == 3) {
                    $end_date = $start_date->copy()->addMonth();
                } elseif ($planId == 4) {
                    $end_date = $start_date->copy()->addMonths(3);
                } elseif ($planId == 5) {
                    $end_date = $start_date->copy()->addMonths(6);
                } elseif ($planId == 6) {
                    $end_date = $start_date->copy()->addMonths(12);
                }

                if ($end_date->lt(Carbon::today())) {
                    $subscription_status = \constSubscription::Expired;
                } else {
                    $subscription_status = \constSubscription::onGoing;
                }

                $subscription = Subscription::create(['member_id'=> $member->id,
                                                    'invoice_id'=> $invoice->id,
                                                    'plan_id'=> (int) $planId,
                                                    'start_date'=> $start_date->toDateString(),
                                                    'end_date'=> $end_date->toDateString(),
                                                    'status'=> $subscription_status,
                                                    'is_renewal'=>'0',
                                                    'created_by'=> Auth::user()->id,
                                                    'updated_by'=> Auth::user()->id,
                                                    ]);

                $invoiceDetail = Invoice_detail::create(['invoice_id'=> $invoice->id,
                                                       'plan_id'=> (int) $planId,
                                                       'item_amount'=> $line->get('total_amount'),
                                                       'item_amount'=> $line->get('total_amount'),
                                                       'created_by'=> Auth::user()->id,
                                                       'updated_by'=> Auth::user()->id,
                                                        ]);

                $paymentDetail = Payment_detail::create(['invoice_id'=> $invoice->id,
                                                         'payment_amount'=> $payment_amount,
                                                         'mode'=> '1',
                                                         'note'=> ' ',
                                                         'created_by'=> Auth::user()->id,
                                                         'updated_by'=> Auth::user()->id,
                                                        ]);

                Setting::where('key', '=', 'invoice_last_number')->update(['value' => $invoiceCounter]);
                Setting::where('key', '=', 'member_last_number')->update(['value' => $memberCounter]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
        }
        echo 'Ho gaya bc';
    }
}
