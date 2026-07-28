<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\TracerSubmission\Pages;

use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\QueryTags\QueryTag;
use MoonShine\UI\Components\Metrics\Wrapped\Metric;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\ActionGroup;
use MoonShine\UI\Fields\ID;
use App\MoonShine\Resources\TracerSubmission\TracerSubmissionResource;
use MoonShine\Support\ListOf;
use Throwable;

class TracerSubmissionIndexPage extends IndexPage
{
    protected bool $isLazy = true;

    protected function buttons(): ListOf
    {
        return parent::buttons();
    }

    protected function filters(): iterable
    {
        return [];
    }

    protected function modifyCreateButton(ActionButtonContract $button): ActionButtonContract
    {
        return ActionButton::emptyHidden();
    }

    protected function modifyEditButton(ActionButtonContract $button): ActionButtonContract
    {
        return ActionButton::emptyHidden();
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
            ...parent::topLayer(),
            ActionGroup::make([
                ActionButton::make('Export XLSX (Excel)', route('export.tracer.csv'))
                    ->icon('document-arrow-down')
                    ->success()
            ])
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
