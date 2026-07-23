<?php

namespace Database\Factories;

use App\Models\AlumniExperience;
use App\Models\AlumniProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AlumniExperience>
 */
class AlumniExperienceFactory extends Factory
{
    protected $model = AlumniExperience::class;

    public function definition(): array
    {
        return [
            'alumni_profile_id' => AlumniProfile::factory(),
            'company_name' => fake()->company(),
            'position' => fake()->jobTitle(),
            'description' => fake()->sentence(),
            'start_date' => now()->subYears(2)->toDateString(),
            'end_date' => now()->subYear()->toDateString(),
            'is_current' => false,
        ];
    }
}
