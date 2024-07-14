<?php

use App\SmsTrigger;
use Illuminate\Database\Seeder;

class SmsTriggersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create SMS Triggers
        $sms_triggers = [
            [
                'name' => 'Member admission (Paid)',
                'alias' => 'member_admission_with_paid_invoice',
                'message' => 'Hi %s , Welcome to %s . Your payment of Rs %u against your invoice no. %s has been received. Thank you and we hope to see you in action soon. Good day!',
                'status' => '0',
            ],
            [
                'name' => 'Member admission (Partial)',
                'alias' => 'member_admission_with_partial_invoice',
                'message' => 'Hi %s , Welcome to %s . Your payment of Rs %u against your invoice no. %s has been received. Outstanding payment to be cleared is Rs %u .Thank you!',
                'status' => '0',
            ],
            [
                'name' => 'Member admission (Unpaid)',
                'alias' => 'member_admission_with_unpaid_invoice',
                'message' => 'Hi %s , Welcome to %s . Your payment of Rs %u is pending against your invoice no. %s . Thank you!',
                'status' => '0',
            ],
            [
                'name' => 'Enquiry placement',
                'alias' => 'enquiry_placement',
                'message' => 'Hi %s , Thank you for your enquiry with %s . We would love to hear from you soon. Good day!',
                'status' => '0',
            ],
            [
                'name' => 'Followup',
                'alias' => 'followup',
                'message' => 'Hi %s , This is regarding the inquiry you placed at %s . Let us know by when would you like to get started? Good day!',
                'status' => '0',
            ],
            [
                'name' => 'Subscription renewal (Paid)',
                'alias' => 'subscription_renewal_with_paid_invoice',
                'message' => 'Hi %s , Your subscription has been renewed successfully. Your payment of Rs %u against your invoice no. %s  has been received. Thank you!',
                'status' => '0',
            ],
            [
                'name' => 'Subscription renewal (Partial)',
                'alias' => 'subscription_renewal_with_partial_invoice',
                'message' => 'Hi %s , Your subscription has been renewed successfully. Your payment of Rs %u against your invoice no. %s has been received. Outstanding payment to be cleared is Rs %u . Thank you!',
                'status' => '0',
            ],
            [
                'name' => 'Subscription renewal (Unpaid)',
                'alias' => 'subscription_renewal_with_unpaid_invoice',
                'message' => 'Hi %s , Your subscription has been renewed successfully. Your payment of Rs %u is pending against your invoice no. %s . Thank you!',
                'status' => '0',
            ],
            [
                'name' => 'Subscription expiring',
                'alias' => 'subscription_expiring',
                'message' => 'Hi %s ,  Last few days to renew your gym subscription. Kindly renew it before %s . Thank you!',
                'status' => '0',
            ],
            [
                'name' => 'Subscription expired',
                'alias' => 'subscription_expired',
                'message' => 'Hi %s , Your gym subscription has been expired on %s . Kindly renew it soon!',
                'status' => '0',
            ],
            [
                'name' => 'Payment recieved',
                'alias' => 'payment_recieved',
                'message' => 'Hi %s , Your payment of Rs %u  has been received against your invoice no. %s . Thank you!',
                'status' => '0',
            ],
            [
                'name' => 'Pending invoice',
                'alias' => 'pending_invoice',
                'message' => 'Hi %s , Your payment of Rs %u is still pending against your invoice no. %s . Kindly clear it soon!',
                'status' => '0',
            ],
            [
                'name' => 'Expense alertexpense_alert',
                'alias' => 'expense_alert',
                'message' => 'Hi , You have an expense lined up for%s of Rs %u on %s . Thank you!',
                'status' => '0',
            ],
            [
                'name' => 'Member birthday wishes',
                'alias' => 'member_birthday',
                'message' => 'Hi %s , Team %s wishes you a very Happy birthday :) Enjoy your day!Payment with cheque',
                'status' => '0',
            ],
            [
                'name' => 'Payment with cheque',
                'alias' => 'payment_with_cheque',
                'message' => 'Hi %s , your cheque of Rs %u with cheque no. %u has been recieved against your invoice no. %s . Regards %s .',
                'status' => '0',
            ],
        ];

        foreach ($sms_triggers as $sms_trigger) {
            SmsTrigger::create($sms_trigger);
        }
    }
}
