<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\MasterMajor;

use Illuminate\Database\Eloquent\Model;
use App\Models\MasterMajor;
use App\MoonShine\Resources\MasterMajor\Pages\MasterMajorIndexPage;
use App\MoonShine\Resources\MasterMajor\Pages\MasterMajorFormPage;
use App\MoonShine\Resources\MasterMajor\Pages\MasterMajorDetailPage;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;

class MasterMajorResource extends ModelResource
{
    protected string $model = MasterMajor::class;
    protected string $title = 'Master Jurusan';
    public string $column = 'name';

    protected function search(): array
    {
        return [
            'id',
            'code',
            'name',
        ];
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
            Text::make('Kode Jurusan', 'code')
                ->required(),
            Text::make('Nama Jurusan', 'name')
                ->required(),
        ];
    }

    public function rules(Model $item): array
    {
        return [
            'code' => ['required', 'string', 'unique:master_majors,code,' . $item->getKey()],
            'name' => ['required', 'string'],
        ];
    }
}
