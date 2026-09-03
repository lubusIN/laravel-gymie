<?php

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

it('renders the admin login page', function (): void {
    $this->withoutVite();

    $this->get(route('filament.admin.auth.login'))
        ->assertOk()
        ->assertSee('Gymie');
});

it('prevents inactive users from accessing the admin panel', function (): void {
    $user = \App\Models\User::factory()->inactive()->create();

    $this->actingAs($user)
        ->get(route('filament.admin.pages.dashboard'))
        ->assertForbidden();
});

it('allows active users to access the admin panel', function (): void {
    $user = \App\Models\User::factory()->create();

    $this->actingAs($user)
        ->get(route('filament.admin.pages.dashboard'))
        ->assertOk();
});
