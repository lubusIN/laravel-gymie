<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Member>
 */
class MemberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'photo' => $this->faker->imageUrl(),
            'code' => $this->faker->unique()->bothify('MEM###'),
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'contact' => $this->faker->phoneNumber(),
            'emergency_contact' => $this->faker->phoneNumber(),
            'health_issue' => $this->faker->optional()->sentence(),
            'gender' => $this->faker->randomElement(['male', 'female', 'other']),
            'dob' => $this->faker->date(),
            'address' => $this->faker->address(),
            'country' => $this->faker->country(),
            'city' => $this->faker->city(),
            'state' => $this->faker->state(),
            'pincode' => $this->faker->postcode(),
            'source' => $this->faker->randomElement(['promotions', 'referral', 'online']),
            'goal' => $this->faker->randomElement(['fitness', 'weight loss', 'muscle gain']),
            'status' => $this->faker->randomElement(['active', 'inactive']),
        ];
    }
}
