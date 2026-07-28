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
            BelongsTo::make('Lowongan', 'jobVacancy', 'title', \App\MoonShine\Resources\JobVacancy\JobVacancyResource::class)->sortable(),
            BelongsTo::make('Pelamar', 'user', 'name', \App\MoonShine\Resources\User\UserResource::class)->sortable(),
            File::make('CV', 'cv_url')->disk('public')->dir('job_applications/cv'),
            Select::make('Status', 'status')->options([
                'pending' => 'Menunggu Review',
                'reviewed' => 'Sedang Direview',
                'accepted' => 'Diterima',
                'rejected' => 'Ditolak'
            ])->updateOnPreview(),
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
