<?php

namespace Tests\Feature\Auth;

use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    public function test_user_can_register_with_valid_data(): void
    {
        $school = School::factory()->create();

        $payload = [
            'name' => 'Budi Tabuti',
            'email' => 'budi@example.com',
            'nisn' => '1234567890',
            'password' => 'Password#123',
            'password_confirmation' => 'Password#123',
            'school_id' => $school->id,
        ];

        $response = $this->postJson('/api/v1/auth/register', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Pendaftaran berhasil. Silakan cek email Anda untuk kode verifikasi.',
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'budi@example.com',
            'nisn' => '1234567890',
            'school_id' => $school->id,
        ]);

        $this->assertDatabaseHas('otp_codes', [
            'user_id' => $response->json('data.user.id'),
        ]);
    }

    public function test_registration_fails_if_email_or_nisn_already_exists(): void
    {
        $school = School::factory()->create();
        
        User::factory()->create([
            'email' => 'existing@example.com',
            'nisn' => '1111111111',
            'school_id' => $school->id,
        ]);

        $responseEmail = $this->postJson('/api/v1/auth/register', [
            'name' => 'Budi Baru',
            'email' => 'existing@example.com',
            'nisn' => '2222222222',
            'password' => 'Password#123',
            'password_confirmation' => 'Password#123',
            'school_id' => $school->id,
        ]);

        $responseEmail->assertStatus(422)->assertJsonValidationErrors(['email']);

        $responseNisn = $this->postJson('/api/v1/auth/register', [
            'name' => 'Budi Baru',
            'email' => 'new@example.com',
            'nisn' => '1111111111',
            'password' => 'Password#123',
            'password_confirmation' => 'Password#123',
            'school_id' => $school->id,
        ]);

        $responseNisn->assertStatus(422)->assertJsonValidationErrors(['nisn']);
    }

    public function test_registration_fails_if_school_id_is_invalid(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Budi Invalid School',
            'email' => 'budi.invalid@example.com',
            'nisn' => '3333333333',
            'password' => 'Password#123',
            'password_confirmation' => 'Password#123',
            'school_id' => 99999,
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['school_id']);
    }
}
