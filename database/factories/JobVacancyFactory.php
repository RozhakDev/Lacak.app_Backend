<?php

namespace Database\Factories;

use App\Models\JobVacancy;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class JobVacancyFactory extends Factory
{
    protected $model = JobVacancy::class;

    public function definition(): array
    {
        return [
            'school_id' => \App\Models\School::factory(),
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
