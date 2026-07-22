<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\AlumniProfile;

use Illuminate\Database\Eloquent\Model;
use App\Models\AlumniProfile;
use App\MoonShine\Resources\AlumniProfile\Pages\AlumniProfileIndexPage;
use App\MoonShine\Resources\AlumniProfile\Pages\AlumniProfileFormPage;
use App\MoonShine\Resources\AlumniProfile\Pages\AlumniProfileDetailPage;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\ActionButtons\ActionButton;
use MoonShine\Support\Enums\Action;
use MoonShine\Support\ListOf;

class AlumniProfileResource extends ModelResource
{
    protected string $model = AlumniProfile::class;
    protected string $title = 'Data Profil Alumni';

    protected function search(): array
    {
        return [
            'id',
            'phone_number',
            'graduation_year',
        ];
    }

    protected function activeActions(): ListOf
    {
        return new ListOf(Action::class, [
            Action::VIEW,
        ]);
    }

    protected function pages(): array
    {
        return [
            AlumniProfileIndexPage::class,
            AlumniProfileFormPage::class,
            AlumniProfileDetailPage::class,
        ];
    }
}
