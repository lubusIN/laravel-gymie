<?php

namespace Database\Factories;

use Database\Factories\Concerns\WithSynchronizedLocation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    use WithSynchronizedLocation;

    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    public $status;
    public $gender;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $this->status = $this->faker->randomElement(['active', 'inactive']);
        $this->gender = $this->faker->randomElement(['male', 'female', 'other']);
        $location = $this->synchronizedLocation();

        return [
            'name' => $this->faker->company,
            'contact' => $location['contact'],
            'address' => $location['address'],
            'country' => $location['country'],
            'state' => $location['state'],
            'city' => $location['city'],
            'pincode' => $location['pincode'],
            'gender' => $this->gender,
            'status' => $this->status,
            'dob' => $this->faker->date(),
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
