<?php

namespace Database\Factories;

use App\Models\TracerSubmission;
use App\Models\AlumniProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TracerSubmission>
 */
class TracerSubmissionFactory extends Factory
{
    protected $model = TracerSubmission::class;

    public function definition(): array
    {
        return [
            'alumni_profile_id' => AlumniProfile::factory(),
            'status' => 'bekerja',
            'submitted_at' => now(),
        ];
    }
}
