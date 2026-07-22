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


/**
 * @extends IndexPage<AlumniProfileResource>
 */
class AlumniProfileIndexPage extends IndexPage
{
    protected bool $isLazy = true;

    /**
     * @return list<FieldContract>
     */
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

    /**
     * @return ListOf<ActionButtonContract>
     */
    protected function buttons(): ListOf
    {
        return parent::buttons();
    }

    /**
     * @return list<FieldContract>
     */
    protected function filters(): iterable
    {
        return [];
    }

    /**
     * @return list<QueryTag>
     */
    protected function queryTags(): array
    {
        return [];
    }

    /**
     * @return list<Metric>
     */
    protected function metrics(): array
    {
        return [];
    }

    /**
     * @param  TableBuilder  $component
     *
     * @return TableBuilder
     */
    protected function modifyListComponent(ComponentContract $component): ComponentContract
    {
        return $component;
    }

    /**
     * @return list<ComponentContract>
     * @throws Throwable
     */
    protected function topLayer(): array
    {
        return [
            ...parent::topLayer()
        ];
    }

    /**
     * @return list<ComponentContract>
     * @throws Throwable
     */
    protected function mainLayer(): array
    {
        return [
            ...parent::mainLayer()
        ];
    }

    /**
     * @return list<ComponentContract>
     * @throws Throwable
     */
    protected function bottomLayer(): array
    {
        return [
            ...parent::bottomLayer()
        ];
    }
}
