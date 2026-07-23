<?php

namespace Tests\Feature\Auth;

use App\Models\OtpCode;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OtpTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    public function test_generating_new_otp_marks_previous_unused_otps_as_used(): void
    {
        $user = User::factory()->unverified()->create([
            'email' => 'otpuser@example.com',
        ]);

        $oldOtp = OtpCode::factory()->create([
            'user_id' => $user->id,
            'code' => '111111',
            'is_used' => false,
            'expires_at' => now()->addMinutes(5),
        ]);

        $authService = app(AuthService::class);
        $authService->generateOtp($user->email, 'verify');

        $this->assertDatabaseHas('otp_codes', [
            'id' => $oldOtp->id,
            'is_used' => true,
        ]);

        $this->assertDatabaseCount('otp_codes', 2);
    }

    public function test_valid_otp_verification_succeeds_and_marks_otp_as_used(): void
    {
        $user = User::factory()->unverified()->create([
            'email' => 'verify@example.com',
        ]);

        $otp = OtpCode::factory()->create([
            'user_id' => $user->id,
            'code' => '654321',
            'is_used' => false,
            'expires_at' => now()->addMinutes(5),
        ]);

        $response = $this->postJson('/api/v1/auth/verify-email', [
            'email' => 'verify@example.com',
            'otp' => '654321',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Verifikasi berhasil. Akun Anda kini aktif.',
            ]);

        $this->assertDatabaseHas('otp_codes', [
            'id' => $otp->id,
            'is_used' => true,
        ]);

        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_reusing_valid_otp_returns_400(): void
    {
        $user = User::factory()->unverified()->create([
            'email' => 'reuse@example.com',
        ]);

        $otp = OtpCode::factory()->used()->create([
            'user_id' => $user->id,
            'code' => '123456',
        ]);

        $response = $this->postJson('/api/v1/auth/verify-email', [
            'email' => 'reuse@example.com',
            'otp' => '123456',
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Kode OTP tidak valid atau sudah kedaluwarsa.',
            ]);
    }

    public function test_expired_otp_returns_400(): void
    {
        $user = User::factory()->unverified()->create([
            'email' => 'expired@example.com',
        ]);

        $otp = OtpCode::factory()->expired()->create([
            'user_id' => $user->id,
            'code' => '999999',
            'is_used' => false,
        ]);

        $response = $this->postJson('/api/v1/auth/verify-email', [
            'email' => 'expired@example.com',
            'otp' => '999999',
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Kode OTP tidak valid atau sudah kedaluwarsa.',
            ]);
    }
}
