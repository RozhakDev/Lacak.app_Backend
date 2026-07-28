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
use App\Models\School;
use App\Models\User;
use MoonShine\Apexcharts\Components\DonutChartMetric;
use MoonShine\Apexcharts\Components\LineChartMetric;
use MoonShine\Apexcharts\Support\SeriesItem;
use App\Models\JobApplication;
use App\Models\Event;
use App\Models\EventParticipant;
use MoonShine\Apexcharts\Components\RawChartMetric;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Dashboard extends Page
{
    protected string $title = 'Analitik BKK Lacak.app';

    public function components(): array
    {
        $user = auth()->user();

        if ($user && $user->hasRole('Super Admin')) {
            return $this->superAdminComponents();
        }

        return $this->adminBkkComponents();
    }

    private function superAdminComponents(): array
    {
        $metrics = Cache::remember('dashboard_metrics_superadmin', now()->addMinutes(10), function () {
            $data = [
                'total_schools' => School::count(),
                'total_users' => User::count(),
                'total_alumni' => AlumniProfile::count(),
                'tracer_masuk' => TracerSubmission::count(),
                'keterserapan' => [
                    'Bekerja' => TracerSubmission::where('status', 'bekerja')->count(),
                    'Kuliah' => TracerSubmission::where('status', 'kuliah')->count(),
                    'Wirausaha' => TracerSubmission::where('status', 'wirausaha')->count(),
                ],
                'top_schools' => DB::table('tracer_submissions')
                    ->join('alumni_profiles', 'tracer_submissions.alumni_profile_id', '=', 'alumni_profiles.id')
                    ->join('users', 'alumni_profiles.user_id', '=', 'users.id')
                    ->join('schools', 'users.school_id', '=', 'schools.id')
                    ->selectRaw('schools.name, COUNT(*) as count')
                    ->groupBy('schools.name')
                    ->orderByDesc('count')
                    ->limit(10)
                    ->pluck('count', 'name')
                    ->toArray()
            ];

            $days = 30;
            $trendTracer = [];
            
            for ($i = $days; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i)->format('Y-m-d');
                $trendTracer[$date] = 0;
            }

            $tracerData = TracerSubmission::where('created_at', '>=', Carbon::now()->subDays($days))
                ->selectRaw('DATE(created_at) as date, count(*) as count')
                ->groupBy('date')
                ->pluck('count', 'date');
                
            foreach($tracerData as $date => $count) {
                if(isset($trendTracer[$date])) $trendTracer[$date] = $count;
            }

            $data['trend_tracer'] = $trendTracer;

            return $data;
        });

        return [
            Grid::make([
                Column::make([
                    ValueMetric::make('Sekolah Terdaftar')
                        ->value($metrics['total_schools'])
                        ->icon('building-library'),
                ])->columnSpan(3),

                Column::make([
                    ValueMetric::make('Total Pengguna (Admin)')
                        ->value($metrics['total_users'])
                        ->icon('users'),
                ])->columnSpan(3),

                Column::make([
                    ValueMetric::make('Total Alumni (Global)')
                        ->value($metrics['total_alumni'])
                        ->icon('academic-cap'),
                ])->columnSpan(3),

                Column::make([
                    ValueMetric::make('Tracer Study Masuk (Global)')
                        ->value($metrics['tracer_masuk'])
                        ->icon('document-text'),
                ])->columnSpan(3),

                Column::make([
                    RawChartMetric::make('Top Performa Sekolah (Pengisian Tracer Terbanyak)')->config(function () use ($metrics) {
                        return [
                            'chart' => [
                                'type' => 'bar',
                                'height' => 350,
                            ],
                            'plotOptions' => [
                                'bar' => [
                                    'horizontal' => true,
                                    'borderRadius' => 4,
                                    'barHeight' => '50%',
                                ]
                            ],
                            'colors' => ['#8b5cf6'],
                            'dataLabels' => [
                                'enabled' => true,
                            ],
                            'series' => [
                                [
                                    'name' => 'Jumlah Tracer',
                                    'data' => array_values($metrics['top_schools'])
                                ]
                            ],
                            'xaxis' => [
                                'categories' => array_keys($metrics['top_schools']),
                            ],
                        ];
                    })
                ])->columnSpan(12),
                
                Column::make([
                    LineChartMetric::make('Tren Pengisian Tracer Global (30 Hari Terakhir)')
                        ->series([
                            SeriesItem::make('Pengisian Tracer', $metrics['trend_tracer'])->line(),
                        ])
                ])->columnSpan(8),

                Column::make([
                    DonutChartMetric::make('Keterserapan Global')
                        ->values($metrics['keterserapan'])
                ])->columnSpan(4),
            ]),
        ];
    }

    private function adminBkkComponents(): array
    {
        $user = auth()->user();
        $cacheKey = 'dashboard_metrics_tenant_' . ($user->school_id ?? 'unknown');

        $metrics = Cache::remember($cacheKey, now()->addMinutes(10), function () {
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

            $tracerData = TracerSubmission::where('created_at', '>=', Carbon::now()->subDays($days))
                ->selectRaw('DATE(created_at) as date, count(*) as count')
                ->groupBy('date')
                ->pluck('count', 'date');
            foreach($tracerData as $date => $count) {
                if(isset($trendTracer[$date])) $trendTracer[$date] = $count;
            }

            $lamaranData = JobApplication::where('created_at', '>=', Carbon::now()->subDays($days))
                ->selectRaw('DATE(created_at) as date, count(*) as count')
                ->groupBy('date')
                ->pluck('count', 'date');
            foreach($lamaranData as $date => $count) {
                if(isset($trendLamaran[$date])) $trendLamaran[$date] = $count;
            }

            $eventData = EventParticipant::where('created_at', '>=', Carbon::now()->subDays($days))
                ->selectRaw('DATE(created_at) as date, count(*) as count')
                ->groupBy('date')
                ->pluck('count', 'date');
            foreach($eventData as $date => $count) {
                if(isset($trendEvent[$date])) $trendEvent[$date] = $count;
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
