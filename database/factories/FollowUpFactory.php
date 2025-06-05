<?php

namespace Database\Factories;

use App\Models\Enquiry;
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
            'date' => $this->faker->date(now()),
            'due_date' => $this->faker->dateTimeBetween('now', '+1 month'),
            'follow_up_method' => $this->faker->randomElement(['call', 'email', 'in_person', 'whatsapp', 'other']),
            'outcome' => $this->faker->sentence(),
            'status' => $this->faker->randomElement(['pending', 'done']),
        ];
    }
}
