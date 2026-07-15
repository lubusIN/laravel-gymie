<?php

namespace App\Jobs;

use App\Services\Email\InvoiceEmailService;
use App\Support\Invoices\InvoiceDocumentNotRenderable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Send the "invoice issued" email (queued).
 */
class SendInvoiceIssuedEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Number of times the job may be attempted.
     */
    public int $tries = 3;

    public int $timeout = 60;

    /** @var list<int> */
    public array $backoff = [10, 60, 300];

    /**
     * Create a new job instance.
     */
    public function __construct(
        public readonly int $invoiceId,
        public readonly string $toEmail,
        public readonly ?string $note = null,
        public readonly ?int $actorId = null,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(InvoiceEmailService $service): void
    {
        try {
            $service->sendInvoiceIssuedEmail(
                invoiceId: $this->invoiceId,
                toEmail: $this->toEmail,
                note: $this->note,
            );
        } catch (InvoiceDocumentNotRenderable $exception) {
            Log::warning('Skipping invoice email: missing required invoice data.', [
                'invoice_id' => $this->invoiceId,
                'missing' => $exception->viewData['missing'],
            ]);
        }
    }

    public function failed(?Throwable $exception): void
    {
        Log::error('Invoice issued email job failed permanently.', [
            'invoice_id' => $this->invoiceId,
            'actor_id' => $this->actorId,
            'exception' => $exception,
        ]);
    }
}
