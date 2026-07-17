<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'major_id' => ['required', 'integer', 'exists:master_majors,id'],
            'graduation_year' => ['required', 'integer', 'min:2000', 'max:' . (date('Y') + 1)],
            'phone_number' => ['required', 'string', 'min:10', 'max:15'],
        ];
    }
}
