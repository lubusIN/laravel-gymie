<?php

use App\Helpers\Helpers;

it('returns countries with iso codes from the local dataset', function (): void {
    $countries = Helpers::getCountriesWithCodes();

    expect($countries)
        ->not->toBeEmpty()
        ->toHaveKey('IN')
        ->toHaveKey('US')
        ->and($countries['IN'])->toBe('India')
        ->and($countries['US'])->toBe('United States');
});
