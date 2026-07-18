<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\MasterMajor;
use App\Http\Resources\MasterMajorResource;
use Illuminate\Http\JsonResponse;

class MasterController extends Controller
{
    public function getMajors(): JsonResponse
    {
        $majors = MasterMajor::orderBy('name', 'asc')->get();

        return $this->successResponse(
            'Data Master Jurusan berhasil diambil', 
            MasterMajorResource::collection($majors)
        );
    }

    public function getTracerOptions(): JsonResponse
    {
        $options = [
            'statuses' => [
                ['value' => 'bekerja', 'label' => 'Bekerja'],
                ['value' => 'kuliah', 'label' => 'Melanjutkan / Kuliah'],
                ['value' => 'wirausaha', 'label' => 'Wirausaha'],
            ],
            'forms' => [
                'bekerja' => [
                    'location_scale' => [
                        ['value' => 'dalam_kota', 'label' => 'Dalam Kota'],
                        ['value' => 'luar_kota', 'label' => 'Luar Kota'],
                    ],
                    'location_country' => [
                        ['value' => 'dalam_negeri', 'label' => 'Dalam Negeri'],
                        ['value' => 'luar_negeri', 'label' => 'Luar Negeri'],
                    ],
                    'salary_range' => [
                        ['value' => '<_umr', 'label' => '< UMR'],
                        ['value' => 'umr_-_5_juta', 'label' => 'UMR - 5 Juta'],
                        ['value' => '5_-_10_juta', 'label' => '5 - 10 Juta'],
                        ['value' => '>_10_juta', 'label' => '> 10 Juta'],
                    ],
                ],
                'wirausaha' => [
                    'ownership_type' => [
                        ['value' => 'sendiri', 'label' => 'Milik Sendiri'],
                        ['value' => 'orang_tua', 'label' => 'Milik Orang Tua/Keluarga'],
                    ],
                    'monthly_omset_range' => [
                        ['value' => '<_5_juta', 'label' => '< 5 Juta'],
                        ['value' => '5_-_15_juta', 'label' => '5 - 15 Juta'],
                        ['value' => '>_15_juta', 'label' => '> 15 Juta'],
                    ],
                ]
            ]
        ];

        return $this->successResponse('Opsi Form Tracer berhasil diambil', $options);
    }
}
