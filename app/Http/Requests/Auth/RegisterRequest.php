<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'nisn' => ['nullable', 'string', 'digits:10', 'unique:users,nisn'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'school_id' => ['required', 'exists:schools,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'nisn.unique' => 'NISN ini sudah terdaftar di sistem kami.',
            'email.unique' => 'Email ini sudah digunakan.',
        ];
    }
}
