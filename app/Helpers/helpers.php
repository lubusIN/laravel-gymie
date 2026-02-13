<?php

namespace App\Helpers;

use NumberFormatter;
use App\Models\Plan;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Number;
use Nnjeim\World\World;

class Helpers
{
    private const DEFAULT_FY_START = '04-01';
    private const DEFAULT_FY_END   = '03-31';
    private const DEFAULT_CURRENCY = 'INR';
    private const SETTINGS_PATH    = 'data/settingsData.json';
    private const DEFAULT_EXPENSE_CATEGORIES = [
        'Rent',
        'Utilities',
        'Supplies',
        'Maintenance',
        'Marketing',
        'Equipment',
        'Payroll',
        'Travel',
        'Other',
    ];

    /** 
     * If non-null, generateLastNumber() and updateLastNumber() will use
     * this array instead of reading the disk JSON.
     */
    protected static ?array $testSettingsOverride = null;

    public static function setTestSettingsOverride(?array $override): void
    {
        self::$testSettingsOverride = $override;
    }

    /**
     * Get the settings data from the JSON file.
     *
     * @return array
     */
    public static function getSettings(): array
    {
        if (static::$testSettingsOverride !== null) {
            return static::$testSettingsOverride;
        }

        $filePath = storage_path(self::SETTINGS_PATH);

        if (!file_exists($filePath)) {
            // Check if example file exists
            $exampleFilePath = storage_path('data/settingsData.json.example');

            if (file_exists($exampleFilePath)) {
                // Copy example file to create settingsData.json
                copy($exampleFilePath, $filePath);
            } else {
                // If no example file, create an empty settings file
                file_put_contents($filePath, json_encode([
                    "general" => [],
                    "invoice" => [],
                    "member" => [],
                    "charges" => [],
                    "expenses" => [],
                    "subscriptions" => [],
                ], JSON_PRETTY_PRINT));
            }
        }
        $settings = json_decode(file_get_contents($filePath), true) ?? [];

        foreach (
            [
                'general',
                'invoice',
                'member',
                'charges',
                'expenses',
                'subscriptions',
            ] as $key
        ) {
            if (! array_key_exists($key, $settings) || ! is_array($settings[$key])) {
                $settings[$key] = [];
            }
        }

        return $settings;
    }

    /**
     * Get a list of all countries.
     *
     * @return array
     */
    public static function getCountries(): array
    {
        $response = World::countries();

        if (!$response->success) {
            return [];
        }

        return collect($response->data)
            ->pluck('name', 'name')
            ->toArray();
    }

    /**
     * Get a list of states for a specific country.
     *
     * @param int|null $countryName The name of the country
     * @return array
     */
    public static function getStates(?string $countryName): array
    {
        if (is_null($countryName)) {
            return [];
        }

        // Retrieve country details to get the country code
        $countryResponse = World::countries([
            'filters' => ['name' => $countryName]
        ]);

        if (!$countryResponse->success || empty($countryResponse->data)) {
            return [];
        }

        $countryId = collect($countryResponse->data)->pluck('id')->first();

        if (!$countryId) {
            return [];
        }

        // Retrieve states using the country code
        $stateResponse = World::states([
            'filters' => ['country_id' => $countryId]
        ]);

        if (!$stateResponse->success) {
            return [];
        }

        return collect($stateResponse->data)
            ->pluck('name', 'name')
            ->toArray();
    }

    /**
     * Get a list of cities for a specific state using its name.
     *
     * @param string|null $stateName The name of the state
     * @return array
     */
    public static function getCities(?string $stateName): array
    {
        if (is_null($stateName)) {
            return [];
        }

        // Retrieve state details to get the state code
        $stateResponse = World::states([
            'filters' => ['name' => $stateName]
        ]);

        if (!$stateResponse->success || empty($stateResponse->data)) {
            return [];
        }

        $stateCode = collect($stateResponse->data)->pluck('id')->first();

        if (!$stateCode) {
            return [];
        }

        // Retrieve cities using the state code
        $cityResponse = World::cities([
            'filters' => ['state_id' => $stateCode]
        ]);

        if (!$cityResponse->success || empty($cityResponse->data)) {
            return [];
        }

        return collect($cityResponse->data)
            ->pluck('name', 'name')
            ->toArray();
    }

    /**
     * Get a list of currencies.
     *
     * @return array
     */
    public static function getCurrencies(): array
    {
        $currencyResponse = World::currencies([
            'fields' => 'name,code',
        ]);

        if (!$currencyResponse->success) {
            return [];
        }

        return collect($currencyResponse->data)
            ->pluck('name', 'code')
            ->toArray();
    }

    /**
     * Get the currency code
     * 
     * @return string
     */
    public static function getCurrencyCode()
    {
        $settings = self::getSettings();
        $currency = $settings['general']['currency'] ?? null;
        return filled($currency) ? $currency : self::DEFAULT_CURRENCY;
    }

    /**
     * Get the number of days before a subscription is considered expiring.
     *
     * @return int
     */
    public static function getSubscriptionExpiringDays(): int
    {
        $settings = self::getSettings();
        $days = $settings['subscriptions']['expiring_days'] ?? 7;

        if (! is_numeric($days)) {
            return 7;
        }

        return max(1, (int) $days);
    }

    /**
     * Get expense categories from settings (fallback to defaults).
     *
     * @return array<int, string>
     */
    public static function getExpenseCategories(): array
    {
        $settings = self::getSettings();
        $categories = $settings['expenses']['categories'] ?? null;

        if (! is_array($categories) || empty($categories)) {
            return self::DEFAULT_EXPENSE_CATEGORIES;
        }

        $normalized = [];
        foreach ($categories as $category) {
            $category = trim((string) $category);
            if ($category === '') {
                continue;
            }
            $normalized[$category] = $category;
        }

        return array_values($normalized);
    }

    /**
     * Get expense category options for selects.
     *
     * @return array<string, string>
     */
    public static function getExpenseCategoryOptions(): array
    {
        $options = [];
        foreach (self::getExpenseCategories() as $category) {
            $key = Str::slug($category);

            if ($key === '') {
                continue;
            }

            $options[$key] = $category;
        }

        return $options;
    }

    public static function getExpenseCategoryLabel(?string $key): ?string
    {
        if (blank($key)) {
            return null;
        }

        return self::getExpenseCategoryOptions()[$key] ?? $key;
    }

    /**
     * Get the discounts from settings.
     *
     * @return array
     */
    public static function getDiscounts(): array
    {
        $settings = self::getSettings();
        $discounts = $settings['charges']['discounts'] ?? [];
        if (! is_array($discounts)) {
            return [];
        }

        $options = [];
        foreach ($discounts as $value) {
            $value              = (string) $value;
            $options[$value]  = Number::percentage($value);
        }

        return $options;
    }

    /**
     * Get the discount amount.
     *
     * @return float
     */
    public static function getDiscountAmount(?float $discount, ?float $fee): float
    {
        $discountAmount = 0.0;
        if (is_numeric($discount) && $discount > 0) {
            $discountAmount = ($fee * $discount) / 100;
        }
        return round($discountAmount, 2);
    }

    /**
     * Get the tax rate from settings.
     *
     * @return float
     */
    public static function getTaxRate(): float
    {
        $settings = self::getSettings();
        $taxRate = $settings['charges']['taxes'] ?? 0.0;
        return is_numeric($taxRate) ? (float)$taxRate : 0.0;
    }

    /**
     * Format the currency value.
     *
     * @param float|null $value
     * @param string|null $currency
     * @return string
     */
    public static function formatCurrency(?float $value, ?string $currency = null): string
    {
        $currency = $currency ?? self::getCurrencyCode();
        return Number::currency($value ?? 0, $currency, null, 0);
    }

    /**
     * Get the currency symbol.
     *
     * @return string The currency symbol.
     */
    public static function getCurrencySymbol(): string
    {
        $currencyCode = self::getCurrencyCode();
        $formatter = new NumberFormatter('en' . "@currency=$currencyCode", NumberFormatter::CURRENCY);
        return $formatter->getSymbol(NumberFormatter::CURRENCY_SYMBOL) ?: '';
    }

    /**
     * Safely parse financial year start and end template dates.
     * 
     * @param array $general General settings array containing 'financial_year_start' and 'financial_year_end'.
     * @return array{start: Carbon, end: Carbon} Array with parsed 'start' and 'end' Carbon instances.
     */
    private static function parseTemplates(array $general): array
    {
        try {
            $start = isset($general['financial_year_start'])
                ? Carbon::parse($general['financial_year_start'])
                : Carbon::createFromFormat('m-d', self::DEFAULT_FY_START);
        } catch (Exception $e) {
            $start = Carbon::createFromFormat('m-d', self::DEFAULT_FY_START);
        }

        try {
            $end = isset($general['financial_year_end'])
                ? Carbon::parse($general['financial_year_end'])
                : Carbon::createFromFormat('m-d', self::DEFAULT_FY_END);
        } catch (Exception $e) {
            $end = Carbon::createFromFormat('m-d', self::DEFAULT_FY_END);
        }

        return ['start' => $start, 'end' => $end];
    }

    /**
     * Parse a date string or return now().
     * 
     * @param string|null $dateString The date string to parse.
     * @return Carbon Parsed Carbon instance, or now() if input is null or empty.
     */
    private static function parseDate(?string $dateString): Carbon
    {
        return $dateString ? Carbon::parse($dateString) : Carbon::now();
    }

    /**
     * Determine fiscal year start and end dates for the given date.
     * 
     * @param Carbon $date The date to calculate the fiscal period for.
     * @return array{0: Carbon, 1: Carbon} Array with [start, end] Carbon instances of the fiscal year.
     */
    private static function getFiscalSpan(Carbon $date): array
    {
        $gen     = self::getSettings()['general'] ?? [];
        $tpl     = self::parseTemplates($gen);

        $year    = $date->year;
        $start   = Carbon::create($year, $tpl['start']->month, $tpl['start']->day);
        $endYear = $tpl['end']->lessThan($tpl['start']) ? $year + 1 : $year;
        $end     = Carbon::create($endYear, $tpl['end']->month, $tpl['end']->day);

        // If the date is before this year's start, shift back a year
        if ($date->lt($start)) {
            $start = $start->subYear();
            $end   = $end->subYear();
        }

        return [$start, $end];
    }

    /**
     * Generate the next sequential identifier for a given type and model.
     *
     * @param  string                 $type         The type identifier used to fetch the corresponding settings.
     * @param  string                 $modelClass   The fully qualified class name of the Eloquent model to query.
     * @param  Carbon|string|null     $dateString   A date (Carbon instance or date string) used to determine the financial year span.
     * @param  string|null            $modalColumn  The model column to search for the last value (e.g. 'number' or 'code').
     * @return string                                The newly generated identifier, prefixed and suffixed as configured.
     */
    public static function generateLastNumber(string $type, string $modelClass, ?string $dateString = null, ?string $modalColumn = "number"): string
    {
        $date          = self::parseDate($dateString);
        [$start, $end] = self::getFiscalSpan($date);
        $settings      = self::getSettings();

        // Resolve the model and its table
        $model         = new $modelClass;
        $table         = $model->getTable();

        // Decide which date column to use
        $dateColumn    = Schema::hasColumn($table, 'date')
            ? 'date'
            : 'created_at';

        $rawPrefix      = data_get($settings, "{$type}.prefix", '');
        $rawSaved      = data_get($settings, "{$type}.last_number", '');

        $prefix         = trim($rawPrefix, '-');
        $prefix         = filled($prefix) ? $prefix : 'GY';
        $sep           = $prefix !== '' ? '-' : '';
        $match         = $prefix . $sep;

        $lastFromDb    = collect(
            app($modelClass)
                ::whereBetween($dateColumn, [$start->toDateString(), $end->toDateString()])
                ->pluck($modalColumn)
        )
            ->map(
                fn($raw) => Str::of($raw)
                    ->whenStartsWith($match, fn($s) => $s->after($match))
                    ->__toString()
            )
            ->map(fn($v) => is_numeric($v) ? (int)$v : 0)
            ->max() ?: 0;

        $lastFromSettings = Str::of($rawSaved)
            ->whenStartsWith($match, fn($s) => $s->after($match))
            ->__toString();
        $lastFromSettings = is_numeric($lastFromSettings)
            ? (int) $lastFromSettings
            : 0;

        $next = max($lastFromDb, $lastFromSettings) + 1;

        return str($prefix)
            ->when($sep !== '', fn($s) => $s->append($sep))
            ->append($next)
            ->__toString();
    }

    /**
     * Persist the last number for a given type if within the current fiscal year.
     * 
     * @param string $type The type of setting to update.
     * @param string $newNumber The new number to set as the last number.
     * @param Carbon|string|null $date The date to check against the financial year.
     * @return void
     */
    public static function updateLastNumber(string $type, string $newNumber, ?string $date = null): void
    {
        $date          = self::parseDate($date);
        [$start, $end] = self::getFiscalSpan($date);

        if (! $date->between($start, $end)) {
            return;
        }

        $settings  = self::getSettings();
        $rawPrefix  = data_get($settings, "{$type}.prefix", 'GY');
        $prefix     = trim($rawPrefix, '-');

        $numericPart = Str::of($newNumber)
            ->match('/(\d+)$/')
            ->__toString();

        if ($numericPart === '' || ! ctype_digit($numericPart)) {
            return;
        }

        $incoming      = (int) $numericPart;
        $rawStored     = data_get($settings, "{$type}.last_number", '');
        $storedNumeric = Str::of($rawStored)
            ->match('/(\d+)$/')
            ->__toString();
        $current       = ctype_digit($storedNumeric) ? (int) $storedNumeric : 0;

        if ($incoming <= $current) {
            return;
        }

        $settings[$type]['last_number'] = $incoming;
        $settings[$type]['prefix']       = $prefix;

        file_put_contents(
            storage_path(self::SETTINGS_PATH),
            json_encode($settings, JSON_PRETTY_PRINT)
        );
    }

    /**
     * Given a subscription start date and a plan ID, return the Y-m-d end date
     * (or empty string if no valid plan/days).
     */
    public static function calculateSubscriptionEndDate(?string $startDate, ?int $planId): string
    {
        if (! $startDate || ! $planId) {
            return '';
        }

        $plan = Plan::find($planId);
        if (! $plan || ! $plan->days) {
            return '';
        }

        return Carbon::parse($startDate)
            ->addDays($plan->days)
            ->toDateString();
    }
}
