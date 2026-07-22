<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\AlumniExperience;

use App\Models\AlumniExperience;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\Switcher;
use MoonShine\Support\Enums\Action;
use MoonShine\Support\ListOf;

class AlumniExperienceResource extends ModelResource
{
    protected string $model = AlumniExperience::class;
    protected string $title = 'Pengalaman';

    protected function activeActions(): ListOf
    {
        return new ListOf(Action::class, [
            Action::VIEW,
        ]);
    }

    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('Perusahaan', 'company_name'),
            Text::make('Posisi', 'position'),
            Date::make('Tgl Mulai', 'start_date')->format('Y-m-d'),
            Date::make('Tgl Selesai', 'end_date')->format('Y-m-d'),
        ];
    }

    protected function detailFields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('Perusahaan', 'company_name'),
            Text::make('Posisi', 'position'),
            Text::make('Deskripsi', 'description'),
            Date::make('Tgl Mulai', 'start_date')->format('Y-m-d'),
            Date::make('Tgl Selesai', 'end_date')->format('Y-m-d'),
            Switcher::make('Sekarang Bekerja?', 'is_current')->readonly(),
        ];
    }
}
