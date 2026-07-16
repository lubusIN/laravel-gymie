<?php

use App\Enums\Status;
use App\Helpers\Helpers;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Carbon::setTestNow(Carbon::parse('2026-02-11 12:00:00'));

    Helpers::setTestSettingsOverride([
        'subscriptions' => [
            'expiring_days' => 7,
        ],
    ]);

    Role::findOrCreate('super_admin', 'web');
});

afterEach(function (): void {
    Helpers::setTestSettingsOverride(null);
    Carbon::setTestNow();
});

it('marks an expired subscription with a renewal as renewed', function (): void {
    $originalSubscription = Subscription::factory()->create([
        'start_date' => now()->subMonths(2),
        'end_date' => now()->subDay(),
        'status' => Status::Ongoing,
    ]);

    $renewalSubscription = Subscription::factory()->create([
        'renewed_from_subscription_id' => $originalSubscription->getKey(),
        'start_date' => now(),
        'end_date' => now()->addMonth(),
        'status' => Status::Ongoing,
    ]);

    $this->artisan('gymie:subscriptions', ['--mark-expired' => true])
        ->expectsOutputToContain('1 renewed')
        ->assertSuccessful();

    expect($originalSubscription->refresh()->status)->toBe(Status::Renewed)
        ->and($renewalSubscription->refresh()->status)->toBe(Status::Ongoing);
});
