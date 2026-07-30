<?php

namespace Tests\Feature\JobVacancy;

use App\Models\JobVacancy;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class JobVacancyValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

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

    public function test_apply_job_happy_path_with_pdf_cv(): void
    {
        [$user, $job] = $this->createUserAndJob();
        Sanctum::actingAs($user);

        $file = UploadedFile::fake()->create('cv.pdf', 500, 'application/pdf');

        $response = $this->postJson("/api/v1/jobs/{$job->id}/apply", [
            'cv' => $file,
            'cover_letter' => 'Saya sangat tertarik dengan posisi ini.',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Berhasil melamar pekerjaan.',
            ]);

        $this->assertDatabaseHas('job_applications', [
            'job_vacancy_id' => $job->id,
            'user_id' => $user->id,
            'status' => 'pending',
        ]);
    }

    public function test_apply_job_happy_path_with_doc_cv(): void
    {
        [$user, $job] = $this->createUserAndJob();
        Sanctum::actingAs($user);

        $file = UploadedFile::fake()->create('cv.doc', 300, 'application/msword');

        $response = $this->postJson("/api/v1/jobs/{$job->id}/apply", [
            'cv' => $file,
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Berhasil melamar pekerjaan.',
            ]);
    }

    public function test_apply_job_happy_path_with_docx_cv(): void
    {
        [$user, $job] = $this->createUserAndJob();
        Sanctum::actingAs($user);

        $file = UploadedFile::fake()->create(
            'cv.docx',
            400,
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        );

        $response = $this->postJson("/api/v1/jobs/{$job->id}/apply", [
            'cv' => $file,
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Berhasil melamar pekerjaan.',
            ]);
    }

    public function test_apply_job_cv_file_size_boundary(): void
    {
        [$user, $job] = $this->createUserAndJob();
        Sanctum::actingAs($user);

        $validSizeFile = UploadedFile::fake()->create('cv_exact_5mb.pdf', 5120, 'application/pdf');
        $responseValid = $this->postJson("/api/v1/jobs/{$job->id}/apply", [
            'cv' => $validSizeFile,
        ]);
        $responseValid->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Berhasil melamar pekerjaan.',
            ]);

        [$user2, $job2] = $this->createUserAndJob();
        Sanctum::actingAs($user2);
        $oversizedFile = UploadedFile::fake()->create('cv_exceed_5mb.pdf', 5121, 'application/pdf');
        $responseExceeded = $this->postJson("/api/v1/jobs/{$job2->id}/apply", [
            'cv' => $oversizedFile,
        ]);
        $responseExceeded->assertStatus(422)
            ->assertJsonValidationErrors(['cv']);
    }

    public function test_apply_job_negative_paths(): void
    {
        [$user, $job] = $this->createUserAndJob();
        Sanctum::actingAs($user);

        $response1 = $this->postJson("/api/v1/jobs/{$job->id}/apply", [
            'cover_letter' => 'Surat tanpa CV',
        ]);
        $response1->assertStatus(422)
            ->assertJsonValidationErrors(['cv']);

        $invalidFile = UploadedFile::fake()->create('cv.txt', 100, 'text/plain');
        $response2   = $this->postJson("/api/v1/jobs/{$job->id}/apply", [
            'cv' => $invalidFile,
        ]);
        $response2->assertStatus(422)
            ->assertJsonValidationErrors(['cv']);

        $oversizedFile = UploadedFile::fake()->create('cv.pdf', 6000, 'application/pdf');
        $response3     = $this->postJson("/api/v1/jobs/{$job->id}/apply", [
            'cv' => $oversizedFile,
        ]);
        $response3->assertStatus(422)
            ->assertJsonValidationErrors(['cv']);
    }
}
