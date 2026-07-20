<?php

declare(strict_types=1);

namespace App\MoonShine\Layouts;

use MoonShine\Laravel\Layouts\AppLayout;
use MoonShine\ColorManager\Palettes\RosePalette;
use MoonShine\ColorManager\ColorManager;
use MoonShine\Contracts\ColorManager\ColorManagerContract;
use MoonShine\Contracts\ColorManager\PaletteContract;
use App\MoonShine\Resources\MasterMajor\MasterMajorResource;
use MoonShine\MenuManager\MenuItem;
use App\MoonShine\Resources\JobVacancy\JobVacancyResource;
use App\MoonShine\Resources\AlumniProfile\AlumniProfileResource;
use App\MoonShine\Resources\TracerSubmission\TracerSubmissionResource;
use MoonShine\MenuManager\MenuGroup;
use App\MoonShine\Resources\User\UserResource;
use App\MoonShine\Resources\Role\RoleResource;

final class MoonShineLayout extends AppLayout
{
    protected ?string $palette = RosePalette::class;

    protected function assets(): array
    {
        return [
            ...parent::assets(),
        ];
    }

    protected function menu(): array
    {
        return [
            MenuGroup::make('Administrasi', [
               MenuItem::make(UserResource::class, 'Pengguna (Users)')->icon('users'),
               MenuItem::make(RoleResource::class, 'Hak Akses (Roles)')->icon('shield-check'),
            ])->icon('users')->canSee(fn() => auth()->user()->hasRole('Super Admin')),

            MenuGroup::make('Data Master', [
                MenuItem::make(MasterMajorResource::class, 'Master Jurusan')->icon('academic-cap'),
            ])->icon('server'),

            MenuGroup::make('Layanan BKK', [
                MenuItem::make(JobVacancyResource::class, 'Bursa Kerja (Loker)')->icon('briefcase'),
            ])->icon('building-office'),

            MenuGroup::make('Laporan Keterserapan', [
                MenuItem::make(AlumniProfileResource::class, 'Profil Alumni')->icon('users'),
                MenuItem::make(TracerSubmissionResource::class, 'Tracer Study')->icon('document-chart-bar'),
            ])->icon('chart-pie'),

        ];
    }

    protected function colors(ColorManagerContract $colorManager): void
    {
        parent::colors($colorManager);
    }

    protected function hasThemes(): bool
    {
        return false;
    }
}
