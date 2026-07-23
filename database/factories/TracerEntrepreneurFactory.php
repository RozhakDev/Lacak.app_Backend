<?php

namespace Database\Factories;

use App\Models\TracerEntrepreneur;
use App\Models\TracerSubmission;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TracerEntrepreneur>
 */
class TracerEntrepreneurFactory extends Factory
{
    protected $model = TracerEntrepreneur::class;

    public function definition(): array
    {
        return [
            'tracer_submission_id' => TracerSubmission::factory(),
            'ownership_type' => 'sendiri',
            'employee_count' => fake()->numberBetween(1, 10),
            'monthly_omset_range' => '5_-_15_juta',
            'business_type' => 'E-Commerce',
        ];
    }
}
