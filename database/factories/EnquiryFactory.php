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
            'contact' => $this->faker->numerify('+##-##########'),
            'date' =>$this->faker->date(now()),
            'gender' => $this->faker->randomElement(['male', 'female', 'other']),
            'dob' => $this->faker->date('d-m-Y', '-15 years'),
            'occupation' => $this->faker->randomElement(['student', 'housewife', 'self_employed', 'professional', 'freelancer', 'others']),
            'status' => $this->faker->randomElement(['lead', 'member', 'lost']),
            'address' => $this->faker->address,
            'country' => $this->faker->country,
            'city' => $this->faker->city,
            'state' => $this->faker->state,
            'pincode' => $this->faker->randomNumber(6, 0),
            'interested_in' => $this->faker->randomElement(['Beginner Pkg', 'Personal trainer', 'Gym', 'Yoga','Fatloss','Others']),
            'source' => $this->faker->randomElement(['promotions', 'word_of_mouth', 'others']),
            'why_do_you_plan_to_join' => $this->faker->randomElement(['fitness', 'body_building', 'fatloss', 'weightgain', 'others']),
            'start_by' => $this->faker->dateTimeBetween('now', '+1 month')->format('d-m-Y'),
        ];
    }
}
