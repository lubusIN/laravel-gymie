<?php

namespace Database\Factories;

use App\Contracts\TenantContext;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
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

        $attributes = [
            'name' => $this->faker->company,
            'contact' => $this->faker->numerify('+##-##########'),
            'address' => $this->faker->address,
            'country' => $this->faker->country,
            'state' => $this->faker->state,
            'city' => $this->faker->city,
            'pincode' => $this->faker->randomNumber(6, 0),
            'gender' => $this->gender,
            'status' => $this->status,
            'dob' => $this->faker->date(),
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];

        if (Schema::hasColumn('users', 'gym_id')) {
            $attributes['gym_id'] = app()->bound(TenantContext::class)
                ? app(TenantContext::class)->gymId()
                : null;
        }

        return $attributes;
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
