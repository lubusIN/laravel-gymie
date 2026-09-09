<?php

/**
 * Regression test: subscription_fee must be immutable after invoice creation.
 *
 * CVE context: PUT /api/v1/invoices/{id} previously accepted subscription_fee
 * as a writable field. Setting it to 0 triggered Invoice::syncFromTransactions(),
 * which recalculated due_amount to 0 — wiping outstanding balances with no
 * payment transaction recorded (CWE-20, business-logic bypass).
 *
 * @see app/Services/Api/Schemas/InvoiceSchema.php  updateRules()
 * @see app/Models/Invoice.php                      boot() → updated hook
 */

use App\Models\Invoice;
use App\Models\Member;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

uses(RefreshDatabase::class);

// ---------------------------------------------------------------------------
// Helpers
// ---------------------------------------------------------------------------

/**
 * Create and authenticate a user with Update:Invoice (and ViewAny/View so we
 * can inspect the response body).
 */
function actingAsInvoiceEditor(): User
{
    app(PermissionRegistrar::class)->forgetCachedPermissions();

    foreach (['ViewAny:Invoice', 'View:Invoice', 'Update:Invoice'] as $name) {
        Permission::findOrCreate($name, 'web');
    }

    $user = User::factory()->create();
    $user->givePermissionTo(['ViewAny:Invoice', 'View:Invoice', 'Update:Invoice']);

    Sanctum::actingAs($user);

    return $user;
}

/**
 * Build a subscription + invoice with a known subscription_fee and no payment.
 *
 * @return array{invoice: Invoice, subscription: Subscription}
 */
function createUnpaidInvoice(float $fee = 1000.0): array
{
    $member = Member::factory()->create();
    $plan   = Plan::factory()->create(['amount' => $fee, 'days' => 30]);

    $subscription = Subscription::factory()->create([
        'member_id'  => $member->id,
        'plan_id'    => $plan->id,
        'start_date' => '2026-03-01',
        'end_date'   => '2026-03-31',
    ]);

    $invoice = Invoice::factory()->create([
        'subscription_id'  => $subscription->id,
        'subscription_fee' => $fee,
        'paid_amount'      => 0,
        'date'             => '2026-03-01',
        'due_date'         => '2026-12-31', // far future — not overdue
    ])->refresh();

    return compact('invoice', 'subscription');
}

// ---------------------------------------------------------------------------
// Tests
// ---------------------------------------------------------------------------

it('does not allow zeroing subscription_fee via the update endpoint (PoC)', function (): void {
    actingAsInvoiceEditor();

    ['invoice' => $invoice] = createUnpaidInvoice(fee: 1000.0);

    $originalFee   = (float) $invoice->subscription_fee;
    $originalTotal = (float) $invoice->total_amount;
    $originalDue   = (float) $invoice->due_amount;

    expect($originalFee)->toBeGreaterThan(0.0)
        ->and($originalDue)->toBeGreaterThan(0.0);

    // Attempt the PoC: set subscription_fee to 0
    $response = $this->putJson("/api/v1/invoices/{$invoice->id}", [
        'subscription_fee' => 0,
    ]);

    $response->assertOk();

    $invoice->refresh();

    expect((float) $invoice->subscription_fee)
        ->toBe($originalFee, 'subscription_fee must not change on update');

    expect((float) $invoice->total_amount)
        ->toBe($originalTotal, 'total_amount must not be recalculated from a zeroed fee');

    expect((float) $invoice->due_amount)
        ->toBe($originalDue, 'due_amount must not be wiped without a payment transaction');
});

it('does not allow changing subscription_fee to an arbitrary non-zero value via the update endpoint', function (): void {
    actingAsInvoiceEditor();

    ['invoice' => $invoice] = createUnpaidInvoice(fee: 1000.0);

    $originalFee = (float) $invoice->subscription_fee;

    $this->putJson("/api/v1/invoices/{$invoice->id}", [
        'subscription_fee' => 9999.99,
    ])->assertOk();

    $invoice->refresh();

    expect((float) $invoice->subscription_fee)
        ->toBe($originalFee, 'subscription_fee must be immutable after creation');
});

it('still allows updating other writable invoice fields', function (): void {
    actingAsInvoiceEditor();

    ['invoice' => $invoice] = createUnpaidInvoice(fee: 500.0);

    $this->putJson("/api/v1/invoices/{$invoice->id}", [
        'due_date'      => '2026-06-30',
        'discount_note' => 'Loyalty discount',
    ])->assertOk();

    $invoice->refresh();

    expect($invoice->due_date?->toDateString())->toBe('2026-06-30')
        ->and($invoice->discount_note)->toBe('Loyalty discount');
});
