<?php

use App\Support\AppConfig;

it('normalizes configured supported locales', function (): void {
    config()->set('app.supported_locales', ['en', ' fr ', '', null, ['ar']]);

    expect(AppConfig::supportedLocales())->toBe(['en', 'fr']);
});

it('falls back to the application locale when supported locales are unavailable', function (mixed $configuredLocales): void {
    config()->set('app.locale', 'de');
    config()->set('app.supported_locales', $configuredLocales);

    expect(AppConfig::supportedLocales())->toBe(['de']);
})->with([
    'missing value' => null,
    'non-array value' => 'en,fr',
    'empty array' => [[]],
    'invalid array values' => [[null, '', ' ', []]],
]);
