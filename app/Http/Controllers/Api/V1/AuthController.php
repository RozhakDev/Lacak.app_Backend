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
            return $this->successResponse('Registrasi berhasil, OTP verifikasi telah dikirim ke email.', $result, 201);
        } catch (Exception $e) {
            return $this->errorResponse('Gagal mendaftarkan akun.', [$e->getMessage()], 500);
        }
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->login($request->validated());
            return $this->successResponse('Login berhasil', $result);
        } catch (Exception $e) {
            $statusCode = $e->getCode() ?: 400;
            return $this->errorResponse($e->getMessage(), [], $statusCode);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return $this->successResponse('Logout berhasil.');
    }

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->generateOtp($request->validated()['email']);
            return $this->successResponse('Kode verifikasi (OTP) telah dikirim ke email Anda.', $result, 200);
        } catch (Exception $e) {
            return $this->errorResponse('Gagal memproses permintaan OTP.', [$e->getMessage()], 500);
        }
    }

    public function resendOtp(ResendOtpRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $result = $this->authService->generateOtp($data['email'], $data['context']);
            return $this->successResponse('Kode verifikasi (OTP) baru telah dikirim ke email Anda.', $result, 200);
        } catch (Exception $e) {
            return $this->errorResponse('Gagal mengirim ulang OTP.', [$e->getMessage()], 500);
        }
    }

    public function verifyEmail(VerifyOtpRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $result = $this->authService->verifyEmail($data['email'], $data['otp']);
            return $this->successResponse('Verifikasi email berhasil. Anda sudah bisa menggunakan sistem.', $result);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), [], 400);
        }
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        try {
            $this->authService->resetPassword($request->validated());
            return $this->successResponse('Password berhasil diubah. Silakan login dengan password baru.');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), [], 400);
        }
    }
}
