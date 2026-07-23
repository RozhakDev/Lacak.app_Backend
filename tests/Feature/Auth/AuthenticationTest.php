<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_user_can_login_with_valid_email(): void
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'identifier' => 'john@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Login berhasil.',
            ])
            ->assertJsonStructure([
                'data' => [
                    'user',
                    'token',
                ]
            ]);
    }

    public function test_user_can_login_with_valid_nisn(): void
    {
        $user = User::factory()->create([
            'nisn' => '1234567890',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'identifier' => '1234567890',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Login berhasil.',
            ])
            ->assertJsonStructure([
                'data' => [
                    'user',
                    'token',
                ]
            ]);
    }

    public function test_login_with_invalid_credentials_returns_401(): void
    {
        $user = User::factory()->create([
            'email' => 'jane@example.com',
            'password' => Hash::make('correct-password'),
            'email_verified_at' => now(),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'identifier' => 'jane@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Email, NISN, atau sandi salah.',
            ]);
    }

    public function test_login_with_non_existent_email_or_nisn_returns_401(): void
    {
        $responseEmail = $this->postJson('/api/v1/auth/login', [
            'identifier' => 'nonexistent@example.com',
            'password' => 'password123',
        ]);

        $responseEmail->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Email, NISN, atau sandi salah.',
            ]);

        $responseNisn = $this->postJson('/api/v1/auth/login', [
            'identifier' => '9999999999',
            'password' => 'password123',
        ]);

        $responseNisn->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Email, NISN, atau sandi salah.',
            ]);
    }

    public function test_login_with_unverified_user_returns_403(): void
    {
        $user = User::factory()->unverified()->create([
            'email' => 'unverified@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'identifier' => 'unverified@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Akun belum terverifikasi. Cek email Anda untuk kode verifikasi.',
            ]);
    }

    protected function clearLoginRateLimiter(): void
    {
        Cache::flush();
        $keys = [
            sha1('|127.0.0.1'),
            sha1('127.0.0.1'),
            sha1('5|1|127.0.0.1'),
            sha1('localhost|127.0.0.1'),
            sha1('POST||api/v1/auth/login|127.0.0.1'),
            sha1('POST|localhost|api/v1/auth/login|127.0.0.1'),
            sha1('POST||v1/auth/login|127.0.0.1'),
            sha1('POST|localhost|v1/auth/login|127.0.0.1'),
            '5,1',
            'throttle:5,1',
        ];

        foreach ($keys as $key) {
            RateLimiter::clear($key);
            RateLimiter::clear('login' . $key);
        }
    }

    public function test_login_rate_limiting_triggers_after_5_attempts(): void
    {
        $this->clearLoginRateLimiter();

        $user = User::factory()->create([
            'email' => 'throttle@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);

        for ($i = 1; $i <= 5; $i++) {
            $response = $this->postJson('/api/v1/auth/login', [
                'identifier' => 'throttle@example.com',
                'password' => 'wrong-password',
            ]);
            $response->assertStatus(401);
        }

        $response6th = $this->postJson('/api/v1/auth/login', [
            'identifier' => 'throttle@example.com',
            'password' => 'wrong-password',
        ]);
        $response6th->assertStatus(429);
    }

}
