<?php

namespace App\Helpers;

use App\Contracts\SequenceRepository;
use App\Contracts\SettingsRepository;
use App\Models\Plan;
use App\Services\JsonSettingsRepository;
use App\Support\Billing\Currency;
use App\Support\Billing\Discounts;
use App\Support\Billing\TaxRate;
use App\Support\Data;
use App\Support\Dates\FiscalYear;
use Carbon\Carbon;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Nnjeim\World\WorldHelper;

class Helpers
{
    private const DEFAULT_CURRENCY = 'INR';

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
     * @param  array<string, mixed>|null  $override
     */
    public static function setTestSettingsOverride(?array $override): void
    {
        /** @var mixed $repository */
        $repository = app(SettingsRepository::class);

        if ($repository instanceof JsonSettingsRepository) {
            $repository->setTestOverride($override);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public static function getSettings(): array
    {
        return app(SettingsRepository::class)->get();
    }

    public static function appTimezone(): string
    {
        return \App\Support\AppConfig::timezone();
    }

    /**
     * Get a list of all countries.
     */
    /**
     * @return array<string, string>
     */
    public static function getCountries(): array
    {
        return collect(self::fallbackCountries())
            ->pluck('name', 'name')
            ->mapWithKeys(fn (mixed $name, mixed $key): array => [\App\Support\Data::string($key) => \App\Support\Data::string($name)])
            ->sortKeys()
            ->all();
    }

    /**
     * Get a list of all countries keyed by ISO2 code.
     *
     * @return array<string, string>
     */
    public static function getCountriesWithCodes(): array
    {
        return collect(self::fallbackCountries())
            ->mapWithKeys(fn (mixed $country): array => [
                \App\Support\Data::string(data_get($country, 'iso2')) => \App\Support\Data::string(data_get($country, 'name')),
            ])
            ->filter(fn (mixed $name, mixed $code): bool => \App\Support\Data::string($code) !== '' && \App\Support\Data::string($name) !== '')
            ->sort()
            ->all();
    }

    /**
     * Get a list of states for a specific country.
     *
     * @param  string|null  $countryName  The name of the country
     */
    /**
     * @return array<string, string>
     */
    public static function getStates(?string $countryName): array
    {
        if (app()->runningUnitTests()) {
            return [];
        }

        if (is_null($countryName)) {
            return [];
        }

        // Retrieve country details to get the country code
        $countryResponse = self::worldResponse('countries', [
            'filters' => ['name' => $countryName],
        ]);

        if (! $countryResponse->success || empty($countryResponse->data)) {
            return [];
        }

        $countryId = collect($countryResponse->data)->pluck('id')->first();

        if (! $countryId) {
            return [];
        }

        // Retrieve states using the country code
        $stateResponse = self::worldResponse('states', [
            'filters' => ['country_id' => $countryId],
        ]);

        if (! $stateResponse->success) {
            return [];
        }

        return collect($stateResponse->data)
            ->pluck('name', 'name')
            ->mapWithKeys(fn (mixed $name, mixed $key): array => [\App\Support\Data::string($key) => \App\Support\Data::string($name)])
            ->all();
    }

    /**
     * Get a list of cities for a specific state using its name.
     *
     * @param  string|null  $stateName  The name of the state
     */
    /**
     * @return array<string, string>
     */
    public static function getCities(?string $stateName): array
    {
        if (app()->runningUnitTests()) {
            return [];
        }

        if (is_null($stateName)) {
            return [];
        }

        // Retrieve state details to get the state code
        $stateResponse = self::worldResponse('states', [
            'filters' => ['name' => $stateName],
        ]);

        if (! $stateResponse->success || empty($stateResponse->data)) {
            return [];
        }

        $stateCode = collect($stateResponse->data)->pluck('id')->first();

        if (! $stateCode) {
            return [];
        }

        // Retrieve cities using the state code
        $cityResponse = self::worldResponse('cities', [
            'filters' => ['state_id' => $stateCode],
        ]);

        if (! $cityResponse->success || empty($cityResponse->data)) {
            return [];
        }

        return collect($cityResponse->data)
            ->pluck('name', 'name')
            ->mapWithKeys(fn (mixed $name, mixed $key): array => [\App\Support\Data::string($key) => \App\Support\Data::string($name)])
            ->all();
    }

    /**
     * Get a list of currencies.
     */
    /**
     * @return array<string, string>
     */
    public static function getCurrencies(): array
    {
        if (app()->runningUnitTests()) {
            return [];
        }

        $currencyResponse = self::worldResponse('currencies', [
            'fields' => 'name,code',
        ]);

        if (! $currencyResponse->success) {
            return [];
        }

        return collect($currencyResponse->data)
            ->pluck('name', 'code')
            ->mapWithKeys(fn (mixed $name, mixed $key): array => [\App\Support\Data::string($key) => \App\Support\Data::string($name)])
            ->all();
    }

    /**
     * Get the currency code
     */
    public static function getCurrencyCode(): string
    {
        return Currency::codeFromSettings(self::getSettings(), self::DEFAULT_CURRENCY);
    }

    /**
     * Get the number of days before a subscription is considered expiring.
     */
    public static function getSubscriptionExpiringDays(): int
    {
        $settings = self::getSettings();
        $subscriptions = is_array($settings['subscriptions'] ?? null) ? $settings['subscriptions'] : [];
        $days = $subscriptions['expiring_days'] ?? 7;

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
        $expenses = is_array($settings['expenses'] ?? null) ? $settings['expenses'] : [];
        $categories = $expenses['categories'] ?? null;

        if (! is_array($categories) || empty($categories)) {
            return self::DEFAULT_EXPENSE_CATEGORIES;
        }

        $normalized = [];
        foreach ($categories as $category) {
            $category = trim(\App\Support\Data::string($category));
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

            $translationKey = "app.expense_categories.{$key}";
            $options[$key] = Lang::has($translationKey) ? __($translationKey) : $category;
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
     */
    /**
     * @return array<string, string>
     */
    public static function getDiscounts(): array
    {
        return Discounts::optionsFromSettings(self::getSettings());
    }

    /**
     * Get the discount amount.
     */
    public static function getDiscountAmount(?float $discount, ?float $fee): float
    {
        return Discounts::amount($discount, $fee);
    }

    /**
     * Get the tax rate from settings.
     */
    public static function getTaxRate(): float
    {
        return TaxRate::fromSettings(self::getSettings());
    }

    /**
     * Format the currency value.
     */
    public static function formatCurrency(?float $value, ?string $currency = null): string
    {
        $currency = $currency ?? self::getCurrencyCode();

        return Currency::format($value, $currency);
    }

    /**
     * Get the currency symbol.
     *
     * @return string The currency symbol.
     */
    public static function getCurrencySymbol(): string
    {
        return Currency::symbol(self::getCurrencyCode());
    }

    /**
     * Parse a date string or return now().
     *
     * @param  string|null  $dateString  The date string to parse.
     * @return Carbon Parsed Carbon instance, or now() if input is null or empty.
     */
    public static function parseDate(?string $dateString): Carbon
    {
        return $dateString ? Carbon::parse($dateString) : Carbon::now();
    }

    /**
     * Determine fiscal year start and end dates for the given date.
     *
     * @param  Carbon  $date  The date to calculate the fiscal period for.
     * @return array{0: Carbon, 1: Carbon} Array with [start, end] Carbon instances of the fiscal year.
     */
    public static function getFiscalSpan(Carbon $date): array
    {
        $generalSettings = self::getSettings()['general'] ?? [];

        return FiscalYear::spanForDate($date, is_array($generalSettings) ? $generalSettings : []);
    }

    /**
     * Generate the next sequential identifier for a given type and model.
     *
     * @param  string  $type  The type identifier used to fetch the corresponding settings.
     * @param  class-string  $modelClass  The fully qualified class name of the Eloquent model to query.
     * @param  string|null  $dateString  A date string used to determine the financial year span.
     * @param  string|null  $modalColumn  The model column to search for the last value (e.g. 'number' or 'code').
     * @return string The newly generated identifier, prefixed and suffixed as configured.
     */
    public static function generateLastNumber(string $type, string $modelClass, ?string $dateString = null, ?string $modalColumn = 'number'): string
    {
        return app(SequenceRepository::class)->generate(
            $type,
            $modelClass,
            $dateString,
            $modalColumn,
        );
    }

    /**
     * Persist the last number for a given type if within the current fiscal year.
     *
     * @param  string  $type  The type of setting to update.
     * @param  string  $newNumber  The new number to set as the last number.
     * @param  string|null  $date  The date to check against the financial year.
     */
    public static function updateLastNumber(string $type, string $newNumber, ?string $date = null): void
    {
        app(SequenceRepository::class)->update($type, $newNumber, $date);
    }

    /**
     * @param  array<string, mixed>  $parameters
     * @return object{success: bool, data: array<int, array<string, mixed>>}
     */
    private static function worldResponse(string $method, array $parameters = []): object
    {
        /** @var object{success: bool, data: array<int, array<string, mixed>>} $response */
        $response = app(WorldHelper::class)->__call($method, [$parameters]);

        return $response;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private static function fallbackCountries(): array
    {
        $path = base_path('vendor/nnjeim/world/resources/json/countries.json');

        if (! is_file($path)) {
            return [];
        }

        /** @var mixed $countries */
        $countries = json_decode((string) file_get_contents($path), true);

        if (! is_array($countries)) {
            return [];
        }

        $filteredCountries = [];

        foreach ($countries as $country) {
            if (is_array($country)) {
                $filteredCountries[] = Data::map($country);
            }
        }

        return $filteredCountries;
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
            ->addDays((int) $plan->days)
            ->toDateString();
    }
}
