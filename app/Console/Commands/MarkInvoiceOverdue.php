<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use Illuminate\Console\Command;

class MarkInvoiceOverdue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gymie:invoices {--mark-overdue : Mark invoices as overdue based on due date}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perform operations on invoices (e.g., mark as overdue)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $updatedCount = Invoice::where('status', 'issue')
            ->whereDate('due_date', '<=', now())
            ->update(['status' => 'overdue']);

        $this->info("{$updatedCount} invoice(s) marked as overdue.");
    }
}
