<?php

declare(strict_types=1);

namespace App\MoonShine\Pages;

use MoonShine\Laravel\Pages\Page;
use MoonShine\Contracts\UI\ComponentContract;

use MoonShine\Components\MoonShineComponent;
use MoonShine\UI\Components\Layout\Grid;
use MoonShine\UI\Components\Layout\Column;
use MoonShine\UI\Components\Metrics\Wrapped\ValueMetric;
use App\Models\AlumniProfile;
use App\Models\JobVacancy;
use App\Models\TracerSubmission;
use App\Models\TracerWork;
use MoonShine\Apexcharts\Components\DonutChartMetric;
use MoonShine\Apexcharts\Components\LineChartMetric;
use MoonShine\Apexcharts\Support\SeriesItem;
use Illuminate\Support\Facades\Cache;

class Dashboard extends Page
{
    protected string $title = 'Analitik BKK Lacak.app';

    public function components(): array
    {
        $metrics = Cache::remember('dashboard_metrics', now()->addMinutes(10), function () {
            return [
                'total_alumni' => AlumniProfile::count(),
                'tracer_masuk' => TracerSubmission::count(),
                'loker_aktif' => JobVacancy::where('is_active', true)->count(),
                'keterserapan' => [
                    'Bekerja' => TracerSubmission::where('status', 'bekerja')->count(),
                    'Kuliah' => TracerSubmission::where('status', 'kuliah')->count(),
                    'Wirausaha' => TracerSubmission::where('status', 'wirausaha')->count(),
                ],
                'gaji_bekerja' => TracerWork::selectRaw('salary_range, count(*) as count')
                                    ->groupBy('salary_range')
                                    ->pluck('count', 'salary_range')
                                    ->toArray()
            ];
        });

        return [
            Grid::make([
                Column::make([
                    ValueMetric::make('Total Alumni Terdaftar')
                        ->value($metrics['total_alumni'])
                        ->icon('users'),
                ])->columnSpan(4),

                Column::make([
                    ValueMetric::make('Tracer Study Masuk')
                        ->value($metrics['tracer_masuk'])
                        ->icon('document-text'),
                ])->columnSpan(4),

                Column::make([
                    ValueMetric::make('Loker BKK Aktif')
                        ->value($metrics['loker_aktif'])
                        ->icon('briefcase'),
                ])->columnSpan(4),

                Column::make([
                    DonutChartMetric::make('Keterserapan Alumni')
                        ->values($metrics['keterserapan'])
                ])->columnSpan(6),

                Column::make([
                    LineChartMetric::make('Distribusi Rentang Gaji (Bekerja)')
                        ->series([
                            SeriesItem::make('Jumlah Alumni', $metrics['gaji_bekerja'])->column()
                        ])
                ])->columnSpan(6),
            ]),
        ];
    }
}
