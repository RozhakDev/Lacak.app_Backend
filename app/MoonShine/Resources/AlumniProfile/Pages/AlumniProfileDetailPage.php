<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\AlumniProfile\Pages;

use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\Contracts\UI\FieldContract;
use App\MoonShine\Resources\AlumniProfile\AlumniProfileResource;
use MoonShine\Support\ListOf;
use MoonShine\UI\Fields\ID;
use Throwable;


/**
 * @extends DetailPage<AlumniProfileResource>
 */
class AlumniProfileDetailPage extends DetailPage
{
    /**
     * @return list<FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            ID::make(),
            \MoonShine\Laravel\Fields\Relationships\BelongsTo::make('User', 'user', 'name', \App\MoonShine\Resources\User\UserResource::class),
            \MoonShine\Laravel\Fields\Relationships\BelongsTo::make('Jurusan', 'major', 'name', \App\MoonShine\Resources\MasterMajor\MasterMajorResource::class),
            \MoonShine\UI\Fields\Text::make('Tahun Lulus', 'graduation_year'),
            \MoonShine\UI\Fields\Text::make('No. HP', 'phone_number'),
            \MoonShine\UI\Fields\Image::make('Foto Profil', 'avatar_url')->disk('public'),
            \MoonShine\UI\Fields\Textarea::make('Tentang Saya', 'about_me'),
            \MoonShine\UI\Fields\Json::make('Keahlian (Skills)', 'skills')->onlyValue(),
            \MoonShine\UI\Fields\Url::make('LinkedIn', 'linkedin_url'),
            \MoonShine\UI\Fields\Url::make('Portofolio', 'portfolio_url'),
            \MoonShine\UI\Fields\File::make('Curriculum Vitae', 'resume_url')->disk('public'),
            \MoonShine\Laravel\Fields\Relationships\HasMany::make('Pengalaman', 'experiences', resource: \App\MoonShine\Resources\AlumniExperience\AlumniExperienceResource::class)
        ];
    }

    protected function buttons(): ListOf
    {
        return parent::buttons();
    }

    /**
     * @param  TableBuilder  $component
     *
     * @return TableBuilder
     */
    protected function modifyDetailComponent(ComponentContract $component): ComponentContract
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
