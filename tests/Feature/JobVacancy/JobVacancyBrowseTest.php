<?php

namespace Tests\Feature\JobVacancy;

use App\Models\JobApplication;
use App\Models\JobVacancy;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class JobVacancyBrowseTest extends TestCase
{
    use RefreshDatabase;

    private function createUserAndJob(array $jobOverrides = []): array
    {
        $school = School::factory()->create();
        $user = User::factory()->create(['school_id' => $school->id]);
        $job = JobVacancy::factory()->create(array_merge([
            'school_id' => $school->id,
            'is_active' => true,
            'expires_at' => now()->addDays(10),
        ], $jobOverrides));

        return [$user, $job, $school];
    }

    public function test_user_can_list_active_job_vacancies(): void
    {
        [$user, $job] = $this->createUserAndJob();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/jobs');

        $response->assertStatus(200)
            ->assertJson(['success' => true, 'message' => 'Daftar lowongan berhasil dimuat.']);
    }

    public function test_user_can_view_active_job_vacancy_detail(): void
    {
        [$user, $job] = $this->createUserAndJob();
        Sanctum::actingAs($user);

        $response = $this->getJson("/api/v1/jobs/{$job->id}");

        $response->assertStatus(200)
            ->assertJson(['success' => true, 'message' => 'Detail lowongan berhasil dimuat.']);
    }

    public function test_inactive_job_returns_404_on_detail(): void
    {
        [$user, $job] = $this->createUserAndJob(['is_active' => false]);
        Sanctum::actingAs($user);

        $response = $this->getJson("/api/v1/jobs/{$job->id}");

        $response->assertStatus(404)
            ->assertJson(['success' => false, 'message' => 'Lowongan tidak ditemukan atau sudah ditutup.']);
    }

    public function test_expired_job_returns_404_on_detail(): void
    {
        [$user, $job] = $this->createUserAndJob(['expires_at' => now()->subDay()]);
        Sanctum::actingAs($user);

        $response = $this->getJson("/api/v1/jobs/{$job->id}");

        $response->assertStatus(404)
            ->assertJson(['success' => false, 'message' => 'Lowongan tidak ditemukan atau sudah ditutup.']);
    }

    public function test_user_cannot_apply_to_same_job_twice(): void
    {
        Storage::fake('public');
        [$user, $job] = $this->createUserAndJob();
        Sanctum::actingAs($user);

        $file = UploadedFile::fake()->create('cv.pdf', 512, 'application/pdf');

        $this->postJson("/api/v1/jobs/{$job->id}/apply", ['cv' => $file]);

        $file2 = UploadedFile::fake()->create('cv2.pdf', 512, 'application/pdf');
        $response = $this->postJson("/api/v1/jobs/{$job->id}/apply", ['cv' => $file2]);

        $response->assertStatus(400)
            ->assertJson(['success' => false, 'message' => 'Anda sudah melamar pekerjaan ini sebelumnya.']);
    }

    public function test_user_cannot_apply_to_expired_job(): void
    {
        Storage::fake('public');
        [$user, $job] = $this->createUserAndJob(['expires_at' => now()->subDay()]);
        Sanctum::actingAs($user);

        $file = UploadedFile::fake()->create('cv.pdf', 512, 'application/pdf');
        $response = $this->postJson("/api/v1/jobs/{$job->id}/apply", ['cv' => $file]);

        $response->assertStatus(404);
    }

    public function test_user_cannot_apply_to_inactive_job(): void
    {
        Storage::fake('public');
        [$user, $job] = $this->createUserAndJob(['is_active' => false]);
        Sanctum::actingAs($user);

        $file = UploadedFile::fake()->create('cv.pdf', 512, 'application/pdf');
        $response = $this->postJson("/api/v1/jobs/{$job->id}/apply", ['cv' => $file]);

        $response->assertStatus(404);
    }

    public function test_user_can_view_own_applications(): void
    {
        [$user, $job, $school] = $this->createUserAndJob();

        JobApplication::factory()->create([
            'user_id' => $user->id,
            'job_vacancy_id' => $job->id,
            'status' => 'pending',
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/jobs/applications');

        $response->assertStatus(200)
            ->assertJson(['success' => true, 'message' => 'Daftar lamaran berhasil dimuat.']);
    }
}
