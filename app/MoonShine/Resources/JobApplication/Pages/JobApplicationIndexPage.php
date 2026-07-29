<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\JobApplication\Pages;

use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\QueryTags\QueryTag;
use MoonShine\UI\Components\Metrics\Wrapped\Metric;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\File;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Text;
use App\MoonShine\Resources\JobVacancy\JobVacancyResource;
use App\MoonShine\Resources\User\UserResource;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use App\MoonShine\Resources\JobApplication\JobApplicationResource;
use MoonShine\Support\ListOf;
use Throwable;

class JobApplicationIndexPage extends IndexPage
{
    protected bool $isLazy = true;

    protected function fields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('Sumber', 'source', fn($item) => $item->jobVacancy?->school_id === null || ($item->jobVacancy?->creator && $item->jobVacancy->creator->hasRole('Super Admin')) ? 'Pusat' : 'Lokal')
                ->badge(fn($status) => $status === 'Pusat' ? 'info' : 'gray'),
            Text::make('Sekolah', 'school_name', fn($item) => $item->jobVacancy?->school?->name ?? '-')
                ->canSee(fn() => auth()->user()->hasRole('Super Admin')),
            BelongsTo::make('Lowongan', 'jobVacancy', 'title', JobVacancyResource::class)->sortable(),
            BelongsTo::make('Pelamar', 'user', 'name', UserResource::class)->sortable(),
            File::make('CV', 'cv_url')->disk('public')->dir('job_applications/cv'),
            Select::make('Status', 'status')->options([
                'pending' => 'Menunggu Review',
                'reviewed' => 'Sedang Direview',
                'accepted' => 'Diterima',
                'rejected' => 'Ditolak'
            ])->readonly(fn($item) => !auth()->user()->can('update', $item))
              ->updateOnPreview(),
        ];
    }

    protected function buttons(): ListOf
    {
        return parent::buttons();
    }

    protected function filters(): iterable
    {
        return [];
    }

    protected function queryTags(): array
    {
        return [];
    }

    protected function metrics(): array
    {
        return [];
    }

    protected function modifyListComponent(ComponentContract $component): ComponentContract
    {
        return $component;
    }

    protected function topLayer(): array
    {
        return [
            ...parent::topLayer()
        ];
    }

    protected function mainLayer(): array
    {
        return [
            ...parent::mainLayer()
        ];
    }

    protected function bottomLayer(): array
    {
        return [
            ...parent::bottomLayer()
        ];
    }
}
