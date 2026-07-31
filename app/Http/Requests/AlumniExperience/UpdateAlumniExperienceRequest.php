<?php

namespace App\Http\Requests\AlumniExperience;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAlumniExperienceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_name' => ['sometimes', 'required', 'string', 'max:255'],
            'position' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'start_date' => ['sometimes', 'required', 'date'],
            'end_date' => [
                'nullable', 
                'date', 
                'after_or_equal:start_date',
                Rule::prohibitedIf(fn() => $this->boolean('is_current')),
            ],
            'is_current' => ['boolean'],
        ];
    }
}
