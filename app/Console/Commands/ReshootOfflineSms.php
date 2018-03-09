<?php

namespace App\Console\Commands;

use App\Sms_log;
use Illuminate\Console\Command;

class ReshootOfflineSms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reshoot:offlineSms';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reshoots all the offline sms ever hour';

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
        try {
            $logs = Sms_log::where('status', '=', 'offline')->get();

            foreach ($logs as $log) {
                $text = urldecode($log->message);
                $sender_id = $log->sender_id;
                \Utilities::retrySms($sender_id, $log->number, $text, $log);
            }
        } catch (\Exception $e) {
        }
    }
}
