<?php

namespace Database\Factories;

use App\Models\TracerWork;
use App\Models\TracerSubmission;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TracerWork>
 */
class TracerWorkFactory extends Factory
{
    protected $model = TracerWork::class;

    public function definition(): array
    {
        return [
            'tracer_submission_id' => TracerSubmission::factory(),
            'location_scale' => 'dalam_kota',
            'location_country' => 'dalam_negeri',
            'field_of_work' => 'Teknologi Informasi',
            'salary_range' => '5_-_10_juta',
            'company_name' => fake()->company(),
            'position' => fake()->jobTitle(),
            'start_date' => fake()->date(),
            'is_linear' => true,
        ];
    }
}
