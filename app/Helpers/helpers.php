<?php

namespace App\Helpers;

use Nnjeim\World\World;

class Helpers
{
    private const SETTINGS_PATH    = 'data/settingsData.json';

    /**
     * Get the settings data from the JSON file.
     *
     * @return array
     */
    public static function getSettings(): array
    {
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
                ], JSON_PRETTY_PRINT));
            }
        }
        return json_decode(file_get_contents($filePath), true) ?? [];
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
}
