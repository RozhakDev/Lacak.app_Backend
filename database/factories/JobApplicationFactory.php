<?php

namespace Database\Factories;

use App\Models\JobVacancy;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class JobApplicationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'job_vacancy_id' => JobVacancy::factory(),
            'user_id' => User::factory(),
            'cv_url' => 'job_applications/cv/sample.pdf',
            'cover_letter' => fake()->paragraph(),
            'status' => 'pending',
        ];
    }
}
