<?php

namespace Database\Factories;

use App\Models\TracerStudy;
use App\Models\TracerSubmission;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TracerStudy>
 */
class TracerStudyFactory extends Factory
{
    protected $model = TracerStudy::class;

    public function definition(): array
    {
        return [
            'tracer_submission_id' => TracerSubmission::factory(),
            'university_name' => fake()->company() . ' University',
            'enrollment_date' => fake()->date(),
            'is_linear' => true,
        ];
    }
}
