<?php

namespace Database\Factories\Concerns;

use App\Helpers\Helpers;
use Throwable;

trait WithSynchronizedLocation
{
    /**
     * Get dependent location data (country, state, city, pincode, address) via Faker and World data.
     *
     * @return array<string, string>
     */
    protected function synchronizedLocation(?string $country = null): array
    {
        if (blank($country)) {
            try {
                $settings = Helpers::getSettings();
                $general = is_array($settings['general'] ?? null) ? $settings['general'] : [];
                $country = $general['country'] ?? null;
            } catch (Throwable) {
                $country = null;
            }
        }

        if (blank($country)) {
            $countries = Helpers::getCountries();
            $country = ! empty($countries)
                ? $this->faker->randomElement(array_keys($countries))
                : $this->faker->country();
        }

        $country = (string) $country;

        $states = Helpers::getStates($country);
        $state = ! empty($states)
            ? (string) $this->faker->randomElement(array_keys($states))
            : (string) $this->faker->state();

        $cities = Helpers::getCities($state);
        $city = ! empty($cities)
            ? (string) $this->faker->randomElement(array_keys($cities))
            : (string) $this->faker->city();

        $address = sprintf(
            '%s, %s, %s',
            $this->faker->streetAddress(),
            $city,
            $state
        );

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
