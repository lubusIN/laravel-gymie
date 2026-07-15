<?php

use App\Contracts\SettingsRepository;
use App\Filament\Pages\Settings;
use Livewire\Livewire;

it('persists settings via the settings repository when saving', function (): void {
    $repository = new class implements SettingsRepository
    {
        /**
         * @var array<string, mixed>
         */
        public array $lastPut = [];

        public function get(): array
        {
            return [
                'general' => [],
                'invoice' => [],
                'member' => [],
                'charges' => [],
                'expenses' => [],
                'subscriptions' => [],
                'payments' => [],
                'notifications' => [
                    'email' => [],
                ],
            ];
        }

        public function put(array $settings): void
        {
            $this->lastPut = $settings;
        }
    };

    app()->instance(SettingsRepository::class, $repository);

    Livewire::test(Settings::class)
        ->set('data', [
            'general' => [
                'financial_year_start' => '2026-04-15',
                'financial_year_end' => null,
                'gym_logo' => ['images/logo.png'],
            ],
            'invoice' => [],
            'member' => [],
            'charges' => [],
            'expenses' => [],
            'subscriptions' => [],
            'payments' => [],
            'notifications' => [
                'email' => [],
            ],
        ])
        ->call('save');

    expect($repository->lastPut)
        ->toHaveKey('general')
        ->and($repository->lastPut['general']['financial_year_start'])->toBe('2026-04-01')
        ->and($repository->lastPut['general']['financial_year_end'])->toBe('2027-03-31')
        ->and($repository->lastPut['general']['gym_logo'])->toBe('images/logo.png');
});
