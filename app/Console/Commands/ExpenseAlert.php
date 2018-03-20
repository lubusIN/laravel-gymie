<?php

namespace App\Console\Commands;

use App\Expense;
use Carbon\Carbon;
use App\SmsTrigger;
use Illuminate\Console\Command;

class ExpenseAlert extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'expense:alert';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Triggers an expense alert sms to the primary contact number';

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
        $expenseAlerts = Expense::where('due_date', '=', Carbon::today()->addDays(1))->where('paid', '=', 0)->get();
        $contact = \Utilities::getSetting('primary_contact');
        $sender_id = \Utilities::getSetting('sms_sender_id');

        $sms_trigger = SmsTrigger::where('alias', '=', 'expense_alert')->first();
        $message = $sms_trigger->message;
        $sms_status = $sms_trigger->status;

        foreach ($expenseAlerts as $expenseAlert) {
            $sms_text = sprintf($message, $expenseAlert->name, $expenseAlert->amount, $expenseAlert->due_date);
            \Utilities::Sms($sender_id, $contact, $sms_text, $sms_status);
        }
    }
}
