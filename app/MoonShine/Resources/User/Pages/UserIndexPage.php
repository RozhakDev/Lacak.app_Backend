<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\User\Pages;

use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\QueryTags\QueryTag;
use MoonShine\UI\Components\Metrics\Wrapped\Metric;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Email;
use MoonShine\Laravel\Fields\Relationships\BelongsToMany;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use App\MoonShine\Resources\Role\RoleResource;
use App\MoonShine\Resources\School\SchoolResource;
use App\MoonShine\Resources\User\UserResource;
use MoonShine\Support\ListOf;
use Throwable;

class UserIndexPage extends IndexPage
{
    protected bool $isLazy = true;

    protected function fields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('Nama Lengkap', 'name')->sortable(),
            Email::make('Email', 'email')->sortable(),
            Text::make('NISN', 'nisn')->sortable(),
            BelongsTo::make('Sekolah', 'school', 'name', SchoolResource::class)
                ->canSee(fn() => auth()->user()->hasRole('Super Admin')),
            BelongsToMany::make('Hak Akses', 'roles', 'name', RoleResource::class)
                ->badge('purple'),
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
