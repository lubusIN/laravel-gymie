<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\SmsTrigger;
use App\Subscription;
use Illuminate\Console\Command;

class SmsExpired extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Triggers sms alerts for expired subscriptions';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $subscriptions = Subscription::where('status', '=', \constSubscription::Expired)->where('end_date', '=', Carbon::today()->subDay())->get();

        $sms_trigger = SmsTrigger::where('alias', '=', 'subscription_expired')->first();
        $message = $sms_trigger->message;
        $sms_status = $sms_trigger->status;
        $sender_id = \Utilities::getSetting('sms_sender_id');

        foreach ($subscriptions as $subscription) {
            $sms_text = sprintf($message, $subscription->member->name, $subscription->end_date->format('d-m-Y'));
            \Utilities::Sms($sender_id, $subscription->member->contact, $sms_text, $sms_status);
        }
    }
}
