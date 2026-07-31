<?php

namespace Tests\Feature\Auth;

use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ResendOtpTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    public function test_user_can_resend_otp_for_verify_context(): void
    {
        $user = User::factory()->unverified()->create([
            'email' => 'resend@example.com',
        ]);

        $response = $this->postJson('/api/v1/auth/resend-otp', [
            'email' => 'resend@example.com',
            'context' => 'verify',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'OTP baru berhasil dikirim ke email Anda.',
            ]);

        $this->assertDatabaseHas('otp_codes', ['user_id' => $user->id]);
    }

    public function test_resend_otp_fails_for_nonexistent_email(): void
    {
        $response = $this->postJson('/api/v1/auth/resend-otp', [
            'email' => 'ghost@example.com',
            'context' => 'verify',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_resend_otp_fails_for_already_verified_account(): void
    {
        User::factory()->create([
            'email' => 'verified@example.com',
            'email_verified_at' => now(),
        ]);

        $response = $this->postJson('/api/v1/auth/resend-otp', [
            'email' => 'verified@example.com',
            'context' => 'verify',
        ]);

        $this->assertContains($response->status(), [400, 500]);
        $response->assertJson(['success' => false]);
    }
}
