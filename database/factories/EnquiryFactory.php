<?php

namespace Database\Factories;

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
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'contact' => $this->faker->phoneNumber,
            'gender' => $this->faker->randomElement(['male', 'female', 'others']),
            'dob' => $this->faker->date('Y-m-d', '-15 years'),
            'occupation' => $this->faker->jobTitle,
            'status' => $this->faker->randomElement(['lead', 'member', 'lost']),
            'address' => $this->faker->address,
            'country' => $this->faker->country,
            'city' => $this->faker->city,
            'state' => $this->faker->state,
            'pincode' => $this->faker->randomNumber(6, 0),
            'interested_in' => $this->faker->randomElement(['Yoga', 'Gym', 'Fat loss', 'Cardio']),
            'source' => $this->faker->randomElement(['Promotions', 'Word of mouth', 'Others']),
            'why_do_you_plan_to_join' => $this->faker->randomElement(['Fitness, Networking', 'Body Building', 'Fatloss', 'Weightgain', 'Others']),
            'start_by' => $this->faker->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
        ];
    }
}
