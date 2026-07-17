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

class Dashboard extends Page
{

    protected string $title = 'Analitik BKK Lacak.app';
    public function components(): array
    {
        return [
            Grid::make([
                Column::make([
                    ValueMetric::make('Total Alumni Terdaftar')
                        ->value(AlumniProfile::count())
                        ->icon('users'),
                ])->columnSpan(4),

                Column::make([
                    ValueMetric::make('Tracer Study Masuk')
                        ->value(TracerSubmission::count())
                        ->icon('document-text'),
                ])->columnSpan(4),

                Column::make([
                    ValueMetric::make('Loker BKK Aktif')
                        ->value(JobVacancy::where('is_active', true)->count())
                        ->icon('briefcase'),
                ])->columnSpan(4),

                Column::make([
                    DonutChartMetric::make('Keterserapan Alumni')
                        ->values([
                            'Bekerja' => TracerSubmission::where('status', 'bekerja')->count(),
                            'Kuliah' => TracerSubmission::where('status', 'kuliah')->count(),
                            'Wirausaha' => TracerSubmission::where('status', 'wirausaha')->count(),
                        ])
                ])->columnSpan(6),

                Column::make([
                    LineChartMetric::make('Distribusi Rentang Gaji (Bekerja)')
                        ->series([
                            SeriesItem::make('Jumlah Alumni', TracerWork::selectRaw('salary_range, count(*) as count')
                                ->groupBy('salary_range')
                                ->pluck('count', 'salary_range')
                                ->toArray())->column()
                        ])
                ])->columnSpan(6),
            ]),
        ];
    }
}
