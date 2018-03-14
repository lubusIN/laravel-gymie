<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\SmsTrigger;
use App\Subscription;
use Illuminate\Console\Command;

class SmsExpiring extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:expiring';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Trigger sms to expiring subscriptions';

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
        $expirings = Subscription::where('end_date', '<=', Carbon::today()->addDays(3))->where('status', '=', \constSubscription::onGoing)->get();

        $sms_trigger = SmsTrigger::where('alias', '=', 'subscription_expiring')->first();
        $message = $sms_trigger->message;
        $sms_status = $sms_trigger->status;
        $sender_id = \Utilities::getSetting('sms_sender_id');

        foreach ($expirings as $expiring) {
            $sms_text = sprintf($message, $expiring->member->name, $expiring->end_date->format('d-m-Y'));
            \Utilities::Sms($sender_id, $expiring->member->contact, $sms_text, $sms_status);
        }
    }
}
