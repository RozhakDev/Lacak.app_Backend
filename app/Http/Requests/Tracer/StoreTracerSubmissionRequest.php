<?php

namespace App\Http\Requests\Tracer;

use Illuminate\Foundation\Http\FormRequest;

class StoreTracerSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'string', 'in:bekerja,kuliah,wirausaha'],

            'location_scale' => ['required_if:status,bekerja', 'in:dalam_kota,luar_kota'],
            'location_country' => ['required_if:status,bekerja', 'in:dalam_negeri,luar_negeri'],
            'field_of_work' => ['required_if:status,bekerja', 'string', 'max:255'],
            'salary_range' => ['required_if:status,bekerja', 'in:<_umr,umr_-_5_juta,5_-_10_juta,>_10_juta'],
            'company_name' => ['required_if:status,bekerja', 'string', 'max:255'],
            'position' => ['required_if:status,bekerja', 'string', 'max:255'],
            'start_date' => ['required_if:status,bekerja', 'date'],
            'is_linear' => ['required_if:status,bekerja,kuliah', 'boolean'],

            'university_name' => ['required_if:status,kuliah', 'string', 'max:255'],
            'enrollment_date' => ['required_if:status,kuliah', 'date'],

            'ownership_type' => ['required_if:status,wirausaha', 'in:sendiri,orang_tua'],
            'employee_count' => ['required_if:status,wirausaha', 'integer', 'min:0'],
            'monthly_omset_range' => ['required_if:status,wirausaha', 'in:<_5_juta,5_-_15_juta,>_15_juta'],
            'business_type' => ['required_if:status,wirausaha', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'required_if' => 'Field :attribute wajib diisi karena status Anda adalah :value.',
        ];
    }
}
