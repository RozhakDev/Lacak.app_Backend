<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Laravel\DependencyInjection\MoonShine;
use MoonShine\Laravel\DependencyInjection\MoonShineConfigurator;

use App\MoonShine\Resources\MasterMajor\MasterMajorResource;
use App\MoonShine\Resources\JobVacancy\JobVacancyResource;
use App\MoonShine\Resources\AlumniProfile\AlumniProfileResource;
use App\MoonShine\Resources\TracerSubmission\TracerSubmissionResource;
use MoonShine\Laravel\Pages\ProfilePage;

use MoonShine\Menu\MenuGroup;
use MoonShine\Menu\MenuItem;
use App\MoonShine\Resources\User\UserResource;
use App\MoonShine\Resources\Role\RoleResource;

class MoonShineServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        moonshine()->resources([

            MasterMajorResource::class,
            JobVacancyResource::class,
            AlumniProfileResource::class,
            TracerSubmissionResource::class,
            UserResource::class,
                RoleResource::class,
            ]);

        moonshine()->pages([
            ProfilePage::class,
        ]);

        moonshineColors()
            ->primary('#1e3a8a')
            ->secondary('#facc15')
            ->darkColors([
                'body' => '#1e1e24',
                'bg' => '#2a2a35',
            ]);
    }
}
