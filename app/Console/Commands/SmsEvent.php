<?php

namespace App\Console\Commands;

use App\Member;
use App\Enquiry;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\SmsEvent as SmsEventModel;

class SmsEvent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:event';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Triggers sms for events scheduled for the day';

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
        $smsevents = SmsEventModel::where('date', '=', Carbon::today())->get();
        $sender_id = \Utilities::getSetting('sms_sender_id');

        foreach ($smsevents as $smsevent) {
            $sms_text = $smsevent->message;
            $sms_status = $smsevent->status;

            foreach (explode(',', $smsevent->send_to) as $sendTo) {
                switch ($sendTo) {
                    case 0:
                        $recievers = Member::where('status', 1)->get();
                        foreach ($recievers as $reciever) {
                            \Utilities::Sms($sender_id, $reciever->contact, $sms_text, $sms_status);
                        }
                        break;

                    case 1:
                        $recievers = Member::where('status', 0)->get();
                        foreach ($recievers as $reciever) {
                            \Utilities::Sms($sender_id, $reciever->contact, $sms_text, $sms_status);
                        }
                        break;

                    case 2:
                        $recievers = Enquiry::where('status', 1)->get();
                        foreach ($recievers as $reciever) {
                            \Utilities::Sms($sender_id, $reciever->contact, $sms_text, $sms_status);
                        }

                        break;

                    case 3:
                        $recievers = Enquiry::where('status', 0)->get();
                        foreach ($recievers as $reciever) {
                            \Utilities::Sms($sender_id, $reciever->contact, $sms_text, $sms_status);
                        }

                        break;

                    default:
                        // code...
                        break;
                }
            }
        }
    }
}
