<?php

namespace App\Services;

use App\Models\User;
use App\Models\AlumniProfile;
use App\Models\TracerSubmission;
use Illuminate\Support\Facades\DB;
use Exception;

class TracerService
{
    public function updateProfile(User $user, array $data): AlumniProfile
    {
        return AlumniProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'major_id' => $data['major_id'],
                'graduation_year' => $data['graduation_year'],
                'phone_number' => $data['phone_number'],
            ]
        );
    }

    public function submitTracer(User $user, array $data): TracerSubmission
    {
        $profile = AlumniProfile::where('user_id', $user->id)->first();
        if (!$profile) {
            throw new Exception('Profil Anda belum lengkap. Silakan lengkapi profil terlebih dahulu.', 403);
        }

        return DB::transaction(function () use ($profile, $data) {
            $submission = TracerSubmission::create([
                'alumni_profile_id' => $profile->id,
                'status' => $data['status'],
                'submitted_at' => now(),
            ]);

            switch ($data['status']) {
                case 'bekerja':
                    $submission->work()->create([
                        'location_scale' => $data['location_scale'],
                        'location_country' => $data['location_country'],
                        'field_of_work' => $data['field_of_work'],
                        'salary_range' => $data['salary_range'],
                        'company_name' => $data['company_name'],
                        'position' => $data['position'],
                        'start_date' => $data['start_date'],
                        'is_linear' => $data['is_linear'],
                    ]);
                    break;

                case 'kuliah':
                    $submission->study()->create([
                        'university_name' => $data['university_name'],
                        'enrollment_date' => $data['enrollment_date'],
                        'is_linear' => $data['is_linear'],
                    ]);
                    break;
                
                case 'wirausaha':
                    $submission->entrepreneur()->create([
                        'ownership_type' => $data['ownership_type'],
                        'employee_count' => $data['employee_count'],
                        'monthly_omset_range' => $data['monthly_omset_range'],
                        'business_type' => $data['business_type'],
                    ]);
                    break;
            }

            return $submission->load(['work', 'study', 'entrepreneur']);
        });
    }
}
