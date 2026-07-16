<?php

namespace App\Support\Billing;

use App\Support\Data;
use Illuminate\Support\Number;

/**
 * Discount helpers.
 *
 * This is intentionally small and stateless so it can be reused from helpers,
 * forms, and services without pulling in application state.
 */
final class Discounts
{
    /**
     * Build discount select options from settings.
     *
     * @param  array<string, mixed>  $settings
     * @return array<array-key, string>
     */
    public static function optionsFromSettings(array $settings): array
    {
        $charges = is_array($settings['charges'] ?? null) ? $settings['charges'] : [];
        $discounts = $charges['discounts'] ?? [];
        if (! is_array($discounts)) {
            return [];
        }

        $options = [];
        foreach ($discounts as $value) {
            $value = Data::float($value);
            $options[(string) $value] = (string) Number::percentage($value);
        }

        return $options;
    }

    /**
     * Calculate the discount amount for a fee at a given percentage.
     */
    public static function amount(?float $discountPercent, ?float $fee): float
    {
        $fee = (float) ($fee ?? 0);
        $discountPercent = (float) ($discountPercent ?? 0);

        $discountAmount = 0.0;
        if ($discountPercent > 0) {
            $discountAmount = ($fee * $discountPercent) / 100;
        }

        return round($discountAmount, 2);
    }
}
