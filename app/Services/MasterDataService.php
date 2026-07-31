<?php

namespace App\Services;

use App\Models\School;
use App\Models\MasterMajor;
use App\Http\Resources\MasterMajorResource;
use App\Http\Resources\SchoolResource;
use Illuminate\Support\Facades\Cache;

class MasterDataService
{
    public function getActiveSchools(): array
    {
        return Cache::rememberForever('master_schools_active', function () {
            $schools = School::where('is_active', true)
                ->orderBy('name')
                ->get();
            return SchoolResource::collection($schools)->resolve();
        });
    }

    public function getMajors(?int $tenantId): array
    {
        if (!$tenantId) {
            return [];
        }

        $cacheKey = "master_majors_{$tenantId}";

        return Cache::rememberForever($cacheKey, function () use ($tenantId) {
            $query = MasterMajor::orderBy('name', 'asc');
            $query->where('school_id', $tenantId);
            
            $majors = $query->get();
            return MasterMajorResource::collection($majors)->resolve();
        });
    }

    public function getTracerOptions(): array
    {
        return [
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
                    'is_linear' => [
                        ['value' => true, 'label' => 'Ya, Sesuai Jurusan'],
                        ['value' => false, 'label' => 'Tidak Sesuai'],
                    ],
                ],
                'kuliah' => [
                    'is_linear' => [
                        ['value' => true, 'label' => 'Ya, Sesuai Jurusan'],
                        ['value' => false, 'label' => 'Tidak Sesuai'],
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
    }
}
