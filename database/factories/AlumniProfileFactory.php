<?php

namespace Database\Factories;

use App\Models\AlumniProfile;
use App\Models\User;
use App\Models\MasterMajor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AlumniProfile>
 */
class AlumniProfileFactory extends Factory
{
    protected $model = AlumniProfile::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'major_id' => MasterMajor::factory(),
            'graduation_year' => fake()->numberBetween(2015, 2025),
            'phone_number' => fake()->phoneNumber(),
            'avatar_url' => null,
            'about_me' => fake()->sentence(),
            'skills' => ['PHP', 'Laravel'],
            'linkedin_url' => fake()->url(),
            'portfolio_url' => fake()->url(),
            'resume_url' => null,
        ];
    }
}
