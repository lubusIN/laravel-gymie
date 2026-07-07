<?php

namespace Database\Factories;

use Database\Factories\Concerns\WithSynchronizedLocation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Member>
 */
class MemberFactory extends Factory
{
    use WithSynchronizedLocation;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $location = $this->synchronizedLocation();

        return [
            // Leave `photo` null so the UI/Table falls back to the default image URL
            'photo' => null,
            'code' => $this->faker->unique()->bothify('MEM###'),
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'contact' => $location['contact'],
            'emergency_contact' => $location['emergency_contact'],
            'health_issue' => $this->faker->optional()->sentence(),
            'gender' => $this->faker->randomElement(['male', 'female', 'other']),
            'dob' => $this->faker->date(),
            'address' => $location['address'],
            'country' => $location['country'],
            'city' => $location['city'],
            'state' => $location['state'],
            'pincode' => $location['pincode'],
            'source' => $this->faker->randomElement(['promotions', 'referral', 'online']),
            'goal' => $this->faker->randomElement(['fitness', 'weight loss', 'muscle gain']),
            'status' => $this->faker->randomElement(['active', 'inactive']),
        ];
    }
}
