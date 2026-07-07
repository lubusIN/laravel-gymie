<?php

use App\Helpers\Helpers;
use Filament\Forms\Components\TextInput;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Nnjeim\World\Actions\SeedAction;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(SeedAction::class);
});

it('returns fallback phone placeholder when no country is configured', function (): void {
    Helpers::setTestSettingsOverride([]);

    expect(Helpers::getPhonePlaceholder())->toBe(__('app.placeholders.example_phone'));
});

it('returns phone placeholder with country code when country is configured in settings', function (): void {
    Helpers::setTestSettingsOverride([
        'general' => [
            'country' => 'India',
        ],
    ]);

    expect(Helpers::getPhonePlaceholder())->toContain('+91');
});

it('automatically sets the phone placeholder on any telephone TextInput component', function (): void {
    Helpers::setTestSettingsOverride([
        'general' => [
            'country' => 'India',
        ],
    ]);

    $input = TextInput::make('contact')->tel();

    expect($input->getPlaceholder())->toBe(Helpers::getPhonePlaceholder())
        ->and($input->getPlaceholder())->toContain('+91');
});

it('does not set the phone placeholder on non-telephone TextInput components', function (): void {
    $input = TextInput::make('name');

    expect($input->getPlaceholder())->toBeNull();
});
