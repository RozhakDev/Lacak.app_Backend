<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Laravel\DependencyInjection\MoonShine;
use MoonShine\Laravel\DependencyInjection\MoonShineConfigurator;
use App\MoonShine\Resources\MoonShineUser\MoonShineUserResource;
use App\MoonShine\Resources\MoonShineUserRole\MoonShineUserRoleResource;
use App\MoonShine\Resources\MasterMajor\MasterMajorResource;
use App\MoonShine\Resources\JobVacancy\JobVacancyResource;
use App\MoonShine\Resources\AlumniProfile\AlumniProfileResource;
use App\MoonShine\Resources\TracerSubmission\TracerSubmissionResource;


use MoonShine\Menu\MenuGroup;
use MoonShine\Menu\MenuItem;

class MoonShineServiceProvider extends ServiceProvider
{
    public function menu(): array
    {
        return [
            MenuGroup::make(static fn() => __('moonshine::ui.resource.system'), [
               MenuItem::make(
                    static fn() => __('moonshine::ui.resource.admins_title'),
                    new MoonShineUserResource()
               ),
                MenuItem::make(
                    static fn() => __('moonshine::ui.resource.role_title'),
                    new MoonShineUserRoleResource()
               ),
            ])->icon('heroicons.cog-6-tooth')->canSee(fn() => auth()->user()->hasRole('Super Admin')),

            MenuGroup::make('Data Master', [
                MenuItem::make('Master Jurusan', new MasterMajorResource())->icon('heroicons.academic-cap'),
            ])->icon('heroicons.server'),

            MenuGroup::make('Layanan BKK', [
                MenuItem::make('Bursa Kerja (Loker)', new JobVacancyResource())->icon('heroicons.briefcase'),
            ])->icon('heroicons.building-office'),

            MenuGroup::make('Laporan Keterserapan', [
                MenuItem::make('Profil Alumni', new AlumniProfileResource())->icon('heroicons.users'),
                MenuItem::make('Tracer Study', new TracerSubmissionResource())->icon('heroicons.document-chart-bar'),
            ])->icon('heroicons.chart-pie'),
        ];
    }

    public function boot(): void
    {
        moonshine()->resources([
            MoonShineUserResource::class,
            MoonShineUserRoleResource::class,
            MasterMajorResource::class,
            JobVacancyResource::class,
            AlumniProfileResource::class,
            TracerSubmissionResource::class,
        ]);

        moonshineColors()
            ->primary('#1e3a8a')
            ->secondary('#facc15');
    }
}
