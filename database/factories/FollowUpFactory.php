<?php

namespace Database\Factories;

use App\Models\Enquiry;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FollowUp>
 */
class FollowUpFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'enquiry_id' => Enquiry::factory(),
            'user_id' => User::factory(),
            'schedule_date' => $this->faker->dateTimeBetween('now', '+1 month'),
            'method' => $this->faker->randomElement(['call', 'email', 'in_person', 'whatsapp', 'other']),
            'outcome' => $this->faker->sentence(),
            'status' => $this->faker->randomElement(['pending', 'done']),
        ];
    }
}
