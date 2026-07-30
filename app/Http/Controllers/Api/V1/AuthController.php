<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\ResendOtpRequest;
use App\Support\AppLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->register($request->validated());

            AppLogger::auth()->info('Pendaftaran akun berhasil.', AppLogger::context([
                'email' => $request->email,
                'school_id' => $request->school_id,
            ]));

            return $this->successResponse('Pendaftaran berhasil. Silakan cek email Anda untuk kode verifikasi.', $result, 201);
        } catch (Exception $e) {
            AppLogger::auth()->error('Pendaftaran akun gagal.', AppLogger::context([
                'email'   => $request->email,
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]));
            return $this->errorResponse('Pendaftaran gagal. Silakan coba lagi.', [], 500);
        }
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->login($request->validated());

            AppLogger::auth()->info('Login berhasil.', AppLogger::context([
                'identifier' => $request->identifier,
            ]));

            return $this->successResponse('Login berhasil.', $result);
        } catch (Exception $e) {
            $statusCode = $e->getCode();
            $statusCode = (is_numeric($statusCode) && $statusCode >= 100 && $statusCode <= 599) ? (int)$statusCode : 400;

            AppLogger::auth()->warning('Percobaan login gagal.', AppLogger::context([
                'identifier' => $request->identifier,
                'reason'     => $e->getMessage(),
            ]));

            return $this->errorResponse($e->getMessage(), [], $statusCode);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        AppLogger::auth()->info('Pengguna logout.', AppLogger::context());

        $request->user()->currentAccessToken()->delete();
        return $this->successResponse('Logout berhasil.');
    }

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->generateOtp($request->validated()['email']);

            AppLogger::auth()->info('OTP lupa sandi berhasil dikirim.', AppLogger::context([
                'email' => $request->email,
            ]));

            return $this->successResponse('Kode OTP berhasil dikirim. Silakan cek email Anda.', $result, 200);
        } catch (Exception $e) {
            AppLogger::auth()->error('Gagal mengirim OTP lupa sandi.', AppLogger::context([
                'email' => $request->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]));
            return $this->errorResponse('Gagal mengirim OTP. Silakan coba lagi nanti.', [], 500);
        }
    }

    public function resendOtp(ResendOtpRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $result = $this->authService->generateOtp($data['email'], $data['context']);

            AppLogger::auth()->info('OTP berhasil dikirim ulang.', AppLogger::context([
                'email' => $data['email'],
                'context' => $data['context'],
            ]));

            return $this->successResponse('OTP baru berhasil dikirim ke email Anda.', $result, 200);
        } catch (Exception $e) {
            AppLogger::auth()->error('Gagal mengirim ulang OTP.', AppLogger::context([
                'email' => $request->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]));
            return $this->errorResponse('Gagal mengirim ulang OTP.', [], 500);
        }
    }

    public function verifyEmail(VerifyOtpRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $result = $this->authService->verifyEmail($data['email'], $data['otp']);

            AppLogger::auth()->info('Verifikasi email berhasil.', AppLogger::context([
                'email' => $data['email'],
            ]));

            return $this->successResponse('Verifikasi berhasil. Akun Anda kini aktif.', $result);
        } catch (Exception $e) {
            AppLogger::auth()->warning('Verifikasi email gagal.', AppLogger::context([
                'email' => $request->email,
                'reason' => $e->getMessage(),
            ]));
            return $this->errorResponse($e->getMessage(), [], 400);
        }
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        try {
            $this->authService->resetPassword($request->validated());

            AppLogger::auth()->info('Reset kata sandi berhasil.', AppLogger::context([
                'email' => $request->email,
            ]));

            return $this->successResponse('Kata sandi berhasil diperbarui. Silakan login kembali.', null);
        } catch (Exception $e) {
            AppLogger::auth()->warning('Reset kata sandi gagal.', AppLogger::context([
                'email' => $request->email,
                'reason' => $e->getMessage(),
            ]));
            return $this->errorResponse($e->getMessage(), [], 400);
        }
    }
}
