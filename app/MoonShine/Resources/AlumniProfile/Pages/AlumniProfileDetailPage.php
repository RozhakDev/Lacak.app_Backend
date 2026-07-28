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
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Textarea;
use MoonShine\UI\Fields\Json;
use MoonShine\UI\Fields\Url;
use MoonShine\UI\Fields\File;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Fields\Relationships\HasMany;
use App\MoonShine\Resources\User\UserResource;
use App\MoonShine\Resources\MasterMajor\MasterMajorResource;
use App\MoonShine\Resources\AlumniExperience\AlumniExperienceResource;
use Throwable;

class AlumniProfileDetailPage extends DetailPage
{
    protected function fields(): iterable
    {
        return [
            ID::make(),
            BelongsTo::make('User', 'user', 'name', UserResource::class),
            BelongsTo::make('Jurusan', 'major', 'name', MasterMajorResource::class),
            Text::make('Tahun Lulus', 'graduation_year'),
            Text::make('No. HP', 'phone_number'),
            Image::make('Foto Profil', 'avatar_url')->disk('public'),
            Textarea::make('Tentang Saya', 'about_me'),
            Json::make('Keahlian (Skills)', 'skills')->onlyValue(),
            Url::make('LinkedIn', 'linkedin_url'),
            Url::make('Portofolio', 'portfolio_url'),
            File::make('Curriculum Vitae', 'resume_url')->disk('public'),
            HasMany::make('Pengalaman', 'experiences', resource: AlumniExperienceResource::class)
        ];
    }

    protected function buttons(): ListOf
    {
        return parent::buttons();
    }

    protected function modifyDetailComponent(ComponentContract $component): ComponentContract
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
