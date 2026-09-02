<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

uses(RefreshDatabase::class);

it('returns analytics payloads', function (): void {
    app(PermissionRegistrar::class)->forgetCachedPermissions();
    Permission::findOrCreate('ViewAny:Analytics', 'web');

    $user = User::factory()->create();
    $user->givePermissionTo('ViewAny:Analytics');
    Sanctum::actingAs($user);

    $this->getJson('/api/v1/analytics/financial')
        ->assertSuccessful()
        ->assertJsonStructure([
            'data' => [
                'range' => ['start', 'end'],
                'metrics' => ['net_revenue', 'collected', 'refunds', 'discounts', 'outstanding', 'expenses', 'profit'],
            ],
        ]);

    $this->getJson('/api/v1/analytics/membership')
        ->assertSuccessful()
        ->assertJsonStructure([
            'data' => [
                'range' => ['start', 'end'],
                'metrics' => ['active_members', 'new_signups', 'renewals', 'expired_not_renewed'],
            ],
        ]);

    $this->getJson('/api/v1/analytics/cashflow-trend')
        ->assertSuccessful()
        ->assertJsonStructure([
            'data' => [
                'grouping',
                'labels',
                'series' => ['collected', 'expenses'],
            ],
        ]);
});
