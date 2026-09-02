<?php

use App\Helpers\Helpers;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

uses(RefreshDatabase::class);

afterEach(function (): void {
    Helpers::setTestSettingsOverride(null);
});

it('reads and updates settings via the API', function (): void {
    app(PermissionRegistrar::class)->forgetCachedPermissions();
    Permission::findOrCreate('View:Settings', 'web');
    Permission::findOrCreate('Update:Settings', 'web');

    $user = User::factory()->create();
    $user->givePermissionTo(['View:Settings', 'Update:Settings']);

    Sanctum::actingAs($user);

    $this->getJson('/api/v1/settings')
        ->assertSuccessful()
        ->assertJsonStructure(['data']);

    $response = $this->putJson('/api/v1/settings', [
        'general' => [
            'gym_name' => 'Demo Gym',
        ],
    ])->assertSuccessful();

    expect((string) $response->json('data.general.gym_name'))->toBe('Demo Gym');
});
