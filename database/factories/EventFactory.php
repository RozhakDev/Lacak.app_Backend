<?php

namespace Database\Factories;

use App\Models\School;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    public function definition(): array
    {
        return [
            'school_id' => School::factory(),
            'created_by' => User::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'event_type' => fake()->randomElement(['job_fair', 'seminar', 'training', 'other']),
            'location_type' => fake()->randomElement(['online', 'offline']),
            'location_details' => fake()->address(),
            'start_date' => now()->addDays(2),
            'end_date' => now()->addDays(5),
            'is_active' => true,
        ];
    }
}
