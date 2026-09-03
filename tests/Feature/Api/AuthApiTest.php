<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

uses(RefreshDatabase::class);

it('logs in and returns a bearer token', function (): void {
    $user = User::factory()->create([
        'email' => 'user@example.com',
    ]);

    $response = $this->postJson('/api/v1/auth/login', [
        'email' => 'user@example.com',
        'password' => 'password',
        'device_name' => 'tests',
    ])->assertSuccessful();

    $token = (string) ($response->json('token') ?? '');

    expect($token)->not->toBeEmpty();

    $this->getJson('/api/v1/me')->assertUnauthorized();

    $this->withToken($token)
        ->getJson('/api/v1/me')
        ->assertSuccessful()
        ->assertJsonPath('data.email', 'user@example.com')
        ->assertJsonStructure([
            'data' => [
                'permissions',
            ],
        ]);
});

it('logs out and revokes the current token', function (): void {
    $user = User::factory()->create();

    $login = $this->postJson('/api/v1/auth/login', [
        'email' => $user->email,
        'password' => 'password',
        'device_name' => 'tests',
    ])->assertSuccessful();

    $token = (string) $login->json('token');

    $this->withToken($token)
        ->postJson('/api/v1/auth/logout')
        ->assertNoContent();

    expect(User::findOrFail($user->id)->tokens()->count())->toBe(0);

    Auth::forgetGuards();

    $this->withToken($token)
        ->getJson('/api/v1/me')
        ->assertUnauthorized();
});

it('rejects login for inactive user', function (): void {
    $user = User::factory()->inactive()->create([
        'email' => 'inactive@example.com',
    ]);

    $this->postJson('/api/v1/auth/login', [
        'email' => 'inactive@example.com',
        'password' => 'password',
        'device_name' => 'tests',
    ])
    ->assertStatus(422)
    ->assertJsonValidationErrors(['email' => 'This account has been deactivated.']);
});
