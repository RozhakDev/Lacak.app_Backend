<?php

namespace Tests\Feature\Auth;

use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    public function test_user_can_request_forgot_password_otp(): void
    {
        $user = User::factory()->create([
            'email' => 'forgot@example.com',
        ]);

        $response = $this->postJson('/api/v1/auth/forgot-password', [
            'email' => 'forgot@example.com',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Kode OTP berhasil dikirim. Silakan cek email Anda.',
            ]);

        $this->assertDatabaseHas('otp_codes', [
            'user_id' => $user->id,
        ]);
    }

    public function test_forgot_password_fails_if_email_not_found(): void
    {
        $response = $this->postJson('/api/v1/auth/forgot-password', [
            'email' => 'doesnotexist@example.com',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_user_can_reset_password_with_valid_otp(): void
    {
        $user = User::factory()->create([
            'email' => 'reset@example.com',
            'password' => Hash::make('OldPassword#123'),
        ]);

        $otp = OtpCode::factory()->create([
            'user_id' => $user->id,
            'code' => '999999',
            'expires_at' => now()->addMinutes(5),
        ]);

        $response = $this->postJson('/api/v1/auth/reset-password', [
            'email' => 'reset@example.com',
            'otp' => '999999',
            'new_password' => 'NewPassword#123',
            'new_password_confirmation' => 'NewPassword#123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Kata sandi berhasil diperbarui. Silakan login kembali.',
            ]);

        $this->assertTrue(Hash::check('NewPassword#123', $user->fresh()->password));
        $this->assertDatabaseMissing('otp_codes', ['id' => $otp->id]);
    }

    public function test_reset_password_fails_with_invalid_otp(): void
    {
        $user = User::factory()->create([
            'email' => 'reset2@example.com',
        ]);

        $response = $this->postJson('/api/v1/auth/reset-password', [
            'email' => 'reset2@example.com',
            'otp' => '000000',
            'new_password' => 'NewPassword#123',
            'new_password_confirmation' => 'NewPassword#123',
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Kode OTP tidak valid atau sudah kedaluwarsa.',
            ]);
    }
}
