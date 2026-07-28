<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\AlumniProfile\Pages;

use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\QueryTags\QueryTag;
use MoonShine\UI\Components\Metrics\Wrapped\Metric;
use MoonShine\UI\Fields\ID;
use App\MoonShine\Resources\AlumniProfile\AlumniProfileResource;
use MoonShine\Support\ListOf;
use Throwable;

class AlumniProfileIndexPage extends IndexPage
{
    protected bool $isLazy = true;

    protected function fields(): iterable
    {
        return [
            ID::make()->sortable(),
            \MoonShine\UI\Fields\Image::make('Foto', 'avatar_url')->disk('public'),
            \MoonShine\Laravel\Fields\Relationships\BelongsTo::make('Nama', 'user', 'name', \App\MoonShine\Resources\User\UserResource::class),
            \MoonShine\Laravel\Fields\Relationships\BelongsTo::make('Jurusan', 'major', 'name', \App\MoonShine\Resources\MasterMajor\MasterMajorResource::class),
            \MoonShine\UI\Fields\Text::make('Tahun Lulus', 'graduation_year')->sortable(),
            \MoonShine\UI\Fields\Text::make('No. HP', 'phone_number'),
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
