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
use App\Models\JobApplication;
use App\Models\Event;
use App\Models\EventParticipant;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;

class Dashboard extends Page
{
    protected string $title = 'Analitik BKK Lacak.app';

    public function components(): array
    {
        $metrics = Cache::remember('dashboard_metrics', now()->addMinutes(10), function () {
            $data = [
                'total_alumni' => AlumniProfile::count(),
                'tracer_masuk' => TracerSubmission::count(),
                'loker_aktif' => JobVacancy::where('is_active', true)->count(),
                'event_aktif' => Event::where('is_active', true)->count(),
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

            $days = 30;
            $trendTracer = [];
            $trendLamaran = [];
            $trendEvent = [];
            
            for ($i = $days; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i)->format('Y-m-d');
                $trendTracer[$date] = 0;
                $trendLamaran[$date] = 0;
                $trendEvent[$date] = 0;
            }

            $tracerData = TracerSubmission::where('created_at', '>=', Carbon::now()->subDays($days))->get()->groupBy(function($item) {
                return $item->created_at->format('Y-m-d');
            });
            foreach($tracerData as $date => $items) {
                if(isset($trendTracer[$date])) $trendTracer[$date] = $items->count();
            }

            $lamaranData = JobApplication::where('created_at', '>=', Carbon::now()->subDays($days))->get()->groupBy(function($item) {
                return $item->created_at->format('Y-m-d');
            });
            foreach($lamaranData as $date => $items) {
                if(isset($trendLamaran[$date])) $trendLamaran[$date] = $items->count();
            }

            $eventData = EventParticipant::where('created_at', '>=', Carbon::now()->subDays($days))->get()->groupBy(function($item) {
                return $item->created_at->format('Y-m-d');
            });
            foreach($eventData as $date => $items) {
                if(isset($trendEvent[$date])) $trendEvent[$date] = $items->count();
            }

            $data['trend_tracer'] = $trendTracer;
            $data['trend_lamaran'] = $trendLamaran;
            $data['trend_event'] = $trendEvent;

            return $data;
        });

        return [
            Grid::make([
                Column::make([
                    ValueMetric::make('Total Alumni Terdaftar')
                        ->value($metrics['total_alumni'])
                        ->icon('users'),
                ])->columnSpan(3),

                Column::make([
                    ValueMetric::make('Tracer Study Masuk')
                        ->value($metrics['tracer_masuk'])
                        ->icon('document-text'),
                ])->columnSpan(3),

                Column::make([
                    ValueMetric::make('Loker BKK Aktif')
                        ->value($metrics['loker_aktif'])
                        ->icon('briefcase'),
                ])->columnSpan(3),

                Column::make([
                    ValueMetric::make('Event & Pelatihan Aktif')
                        ->value($metrics['event_aktif'])
                        ->icon('calendar'),
                ])->columnSpan(3),

                Column::make([
                    LineChartMetric::make('Tren Aktivitas Alumni (30 Hari Terakhir)')
                        ->series([
                            SeriesItem::make('Pengisian Tracer', $metrics['trend_tracer'])->line(),
                            SeriesItem::make('Lamaran Masuk', $metrics['trend_lamaran'])->line(),
                            SeriesItem::make('Pendaftaran Event', $metrics['trend_event'])->line(),
                        ])
                ])->columnSpan(12),

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
