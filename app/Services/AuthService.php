<?php

namespace App\Services;

use App\Models\User;
use App\Models\OtpCode;
use App\Mail\SendVerificationEmailMail;
use App\Mail\SendResetPasswordMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Exception;

class AuthService
{
    public function register(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'nisn' => $data['nisn'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            $user->assignRole('User');

            $this->generateOtp($user->email, 'verify');

            return [
                'user' => $user->load('roles'),
                'email' => $user->email,
                'message' => 'Silakan periksa email Anda untuk verifikasi OTP.',
            ];
        });
    }

    public function login(array $credentials): array
    {
        $identifier = $credentials['identifier'];

        $user = User::where('email', $identifier)
                    ->orWhere('nisn', $identifier)
                    ->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw new Exception('Email, NISN, atau sandi salah.', 401);
        }

        if (is_null($user->email_verified_at)) {
            throw new Exception('Akun belum terverifikasi. Cek email Anda untuk kode verifikasi.', 403);
        }

        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user->load('roles'),
            'token' => $token,
        ];
    }

    public function generateOtp(string $email, string $context = 'reset'): array
    {
        $user = User::where('email', $email)->first();

        if ($context === 'verify' && !is_null($user->email_verified_at)) {
            throw new Exception('Akun ini sudah diverifikasi.', 400);
        }

        OtpCode::where('user_id', $user->id)
            ->where('is_used', false)
            ->update(['is_used' => true]);

        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        $otpRecord = OtpCode::create([
            'user_id' => $user->id,
            'code' => $otp,
            'expires_at' => now()->addMinutes(5),
        ]);

        if ($context === 'verify') {
            Mail::to($user->email)->send(new SendVerificationEmailMail($otp, $user->name));
        } else {
            Mail::to($user->email)->send(new SendResetPasswordMail($otp, $user->name));
        }

        return [
            'email' => $email,
            'expires_in' => '5 minutes'
        ];
    }



    public function verifyEmail(string $email, string $otpCode): array
    {
        $user = User::where('email', $email)->first();

        $validOtp = OtpCode::where('user_id', $user->id)
            ->where('code', $otpCode)
            ->where('is_used', false)
            ->where('expires_at', '>=', now())
            ->first();

        if (!$validOtp) {
            throw new Exception('Kode OTP tidak valid atau sudah kedaluwarsa.', 400);
        }

        return DB::transaction(function () use ($validOtp, $user) {
            $validOtp->update(['is_used' => true]);
            $user->update(['email_verified_at' => now()]);
            
            $token = $user->createToken('auth_token')->plainTextToken;

            return [
                'user' => $user->load('roles'),
                'token' => $token
            ];
        });
    }

    public function resetPassword(array $data): void
    {
        $user = User::where('email', $data['email'])->first();

        $validOtp = OtpCode::where('user_id', $user->id)
            ->where('code', $data['otp'])
            ->where('is_used', false)
            ->where('expires_at', '>=', now())
            ->first();

        if (!$validOtp) {
            throw new Exception('Kode OTP tidak valid atau sudah kedaluwarsa.', 400);
        }

        DB::transaction(function () use ($validOtp, $user, $data) {
            $validOtp->update(['is_used' => true]);
            $user->update([
                'password' => Hash::make($data['new_password'])
            ]);
        });
    }
}