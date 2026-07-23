<?php

namespace Database\Factories;

use App\Models\JobVacancy;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JobVacancy>
 */
class JobVacancyFactory extends Factory
{
    protected $model = JobVacancy::class;

    public function definition(): array
    {
        return [
            'created_by' => User::factory(),
            'title' => fake()->jobTitle(),
            'company_name' => fake()->company(),
            'images' => [],
            'description' => fake()->paragraph(),
            'requirements' => fake()->sentence(),
            'is_active' => true,
            'expires_at' => now()->addDays(30)->toDateString(),
        ];
    }
}
