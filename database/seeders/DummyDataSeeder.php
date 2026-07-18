<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\MasterMajor;
use App\Models\AlumniProfile;
use App\Models\TracerSubmission;
use App\Models\TracerWork;
use App\Models\TracerStudy;
use App\Models\TracerEntrepreneur;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        
        $majors = MasterMajor::pluck('id')->toArray();
        if (empty($majors)) {
            $this->command->warn('Tabel master_majors kosong. Silakan jalankan MasterMajorSeeder terlebih dahulu.');
            return;
        }

        $password = Hash::make('password123');
        $statuses = ['bekerja', 'kuliah', 'wirausaha'];

        for ($i = 0; $i < 50; $i++) {
            $user = User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'nisn' => $faker->unique()->numerify('##########'),
                'password' => $password,
                'email_verified_at' => Carbon::now(),
            ]);

            $user->assignRole('User');

            if ($faker->boolean(80)) {
                $alumniProfile = AlumniProfile::create([
                    'user_id' => $user->id,
                    'major_id' => $faker->randomElement($majors),
                    'graduation_year' => $faker->numberBetween(2018, 2024),
                    'phone_number' => $faker->numerify('08##########'),
                ]);

                if ($faker->boolean(80)) {
                    $status = $faker->randomElement($statuses);
                    
                    $submittedAt = $faker->dateTimeBetween('-1 year', 'now');

                    $submission = TracerSubmission::create([
                        'alumni_profile_id' => $alumniProfile->id,
                        'status' => $status,
                        'submitted_at' => $submittedAt,
                        'created_at' => $submittedAt,
                        'updated_at' => $submittedAt,
                    ]);

                    if ($status === 'bekerja') {
                        TracerWork::create([
                            'tracer_submission_id' => $submission->id,
                            'location_scale' => $faker->randomElement(['dalam_kota', 'luar_kota']),
                            'location_country' => $faker->randomElement(['dalam_negeri', 'luar_negeri']),
                            'field_of_work' => $faker->jobTitle,
                            'salary_range' => $faker->randomElement(['<_umr', 'umr_-_5_juta', '5_-_10_juta', '>_10_juta']),
                            'company_name' => $faker->company,
                            'position' => $faker->jobTitle,
                            'start_date' => $faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d'),
                            'is_linear' => $faker->boolean(70),
                        ]);
                    } elseif ($status === 'kuliah') {
                        TracerStudy::create([
                            'tracer_submission_id' => $submission->id,
                            'university_name' => 'Universitas ' . $faker->city,
                            'enrollment_date' => $faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d'),
                            'is_linear' => $faker->boolean(80),
                        ]);
                    } elseif ($status === 'wirausaha') {
                        TracerEntrepreneur::create([
                            'tracer_submission_id' => $submission->id,
                            'ownership_type' => $faker->randomElement(['sendiri', 'orang_tua']),
                            'employee_count' => $faker->numberBetween(0, 50),
                            'monthly_omset_range' => $faker->randomElement(['<_5_juta', '5_-_15_juta', '>_15_juta']),
                            'business_type' => $faker->randomElement(['F&B', 'Retail', 'Jasa', 'Teknologi', 'Fashion']),
                        ]);
                    }
                }
            }
        }
    }
}
