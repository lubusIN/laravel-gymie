<?php

namespace App\Support\Billing;

/**
 * Tax helpers.
 */
final class TaxRate
{
    /**
     * Resolve the tax rate percentage from settings.
     *
     * @param  array<string, mixed>  $settings
     */
    public static function fromSettings(array $settings): float
    {
        $charges = is_array($settings['charges'] ?? null) ? $settings['charges'] : [];
        $taxRate = $charges['taxes'] ?? 0.0;

        return is_numeric($taxRate) ? (float) $taxRate : 0.0;
    }
}
