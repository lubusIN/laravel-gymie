<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Enquiry>
 */
class EnquiryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'contact' => $this->faker->numerify('+##-##########'),
            'date' => $this->faker->date(now()),
            'gender' => $this->faker->randomElement(['male', 'female', 'other']),
            'dob' => $this->faker->date('d-m-Y', '-15 years'),
            'status' => $this->faker->randomElement(['lead', 'member', 'lost']),
            'address' => $this->faker->address,
            'country' => $this->faker->country,
            'city' => $this->faker->city,
            'state' => $this->faker->state,
            'pincode' => $this->faker->randomNumber(6, 0),
            'interested_in' => Service::inRandomOrder()->limit(rand(1, 5))->pluck('name')->toArray(),
            'source' => $this->faker->randomElement(['promotions', 'word_of_mouth', 'others']),
            'goal' => $this->faker->randomElement(['fitness', 'body_building', 'fatloss', 'weightgain', 'others']),
            'start_by' => $this->faker->dateTimeBetween('now', '+1 month')->format('d-m-Y'),
        ];
    }
}
