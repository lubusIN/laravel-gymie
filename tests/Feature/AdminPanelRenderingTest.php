<?php

it('renders the admin login page', function (): void {
    $this->withoutVite();

    $this->get(route('filament.admin.auth.login'))
        ->assertOk()
        ->assertSee('Gymie');
});
