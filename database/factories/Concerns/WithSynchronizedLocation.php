<?php

namespace Database\Factories\Concerns;

use App\Helpers\Helpers;

trait WithSynchronizedLocation
{
    /**
     * Get dependent location data (country, state, city, pincode, address) via Faker and World data.
     *
     * @param  string|null  $country  Optional country name to enforce.
     * @return array<string, string>
     */
    protected function synchronizedLocation(?string $country = null): array
    {
        // 1. Resolve Country (from parameter, settings, world database, or Faker)
        if (blank($country)) {
            try {
                $settings = Helpers::getSettings();
                $general = is_array($settings['general'] ?? null) ? $settings['general'] : [];
                $country = $general['country'] ?? null;
            } catch (\Throwable $e) {
                // Ignore settings lookup failures during unit tests
            }
        }

        if (blank($country)) {
            $countries = Helpers::getCountries();
            $country = ! empty($countries)
                ? $this->faker->randomElement(array_keys($countries))
                : $this->faker->country();
        }

        $country = (string) $country;

        // 2. Resolve State (dependent on Country)
        $states = Helpers::getStates($country);
        $state = ! empty($states)
            ? (string) $this->faker->randomElement(array_keys($states))
            : (string) $this->faker->state();

        // 3. Resolve City (dependent on State)
        $cities = Helpers::getCities($state);
        $city = ! empty($cities)
            ? (string) $this->faker->randomElement(array_keys($cities))
            : (string) $this->faker->city();

        // 4. Resolve Address (dependent on City and State)
        $address = sprintf(
            '%s, %s, %s',
            $this->faker->streetAddress(),
            $city,
            $state
        );

        // 5. Resolve Contact Phone Numbers (dependent on Country Phone Code)
        $phoneCode = Helpers::getCountryPhoneCode($country);
        $contact = ! blank($phoneCode)
            ? sprintf('+%s-%s', $phoneCode, $this->faker->numerify('##########'))
            : $this->faker->numerify('+##-##########');

        $emergencyContact = ! blank($phoneCode)
            ? sprintf('+%s-%s', $phoneCode, $this->faker->numerify('##########'))
            : $this->faker->numerify('+##-##########');

        return [
            'country' => $country,
            'state' => $state,
            'city' => $city,
            'pincode' => $this->faker->postcode(),
            'address' => $address,
            'contact' => $contact,
            'emergency_contact' => $emergencyContact,
        ];
    }

    /**
     * Set a location state for a specific country.
     */
    public function fromCountry(string $country): static
    {
        return $this->state(fn (array $attributes): array => $this->synchronizedLocation($country));
    }
}
