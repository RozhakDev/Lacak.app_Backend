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

    public function getActiveActions(): array
    {
        return ['view'];
    }

        protected function indexFields(): iterable
    {
        return $this->myFields();
    }

    protected function formFields(): iterable
    {
        return $this->myFields();
    }

    protected function detailFields(): iterable
    {
        return $this->myFields();
    }

    private function myFields(): array
    {
        return [
            ID::make()->sortable(),
            Text::make('User (NISN)', 'user.nisn'),
            Text::make('Nama', 'user.name'),
            Text::make('Jurusan', 'major.name'),
            Text::make('Tahun Lulus', 'graduation_year')->sortable(),
            Text::make('No. Telepon', 'phone_number'),
        ];
    }
}
