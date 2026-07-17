<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\TracerSubmission;

use Illuminate\Database\Eloquent\Model;
use App\Models\TracerSubmission;
use App\MoonShine\Resources\TracerSubmission\Pages\TracerSubmissionIndexPage;
use App\MoonShine\Resources\TracerSubmission\Pages\TracerSubmissionFormPage;
use App\MoonShine\Resources\TracerSubmission\Pages\TracerSubmissionDetailPage;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Enum;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Fields\Relationships\HasOne;

class TracerSubmissionResource extends ModelResource
{
    protected string $model = TracerSubmission::class;
    protected string $title = 'Laporan Tracer Study';

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
            BelongsTo::make('Alumni', 'alumniProfile', function($profile) {
                return $profile->user->name . ' (' . $profile->user->nisn . ')';
            }),
            Text::make('Status', 'status')
                ->badge(fn($status, $field) => match ($status) {
                    'bekerja' => 'success',
                    'kuliah' => 'info',
                    'wirausaha' => 'warning',
                    default => 'gray'
                }),
            Text::make('Tanggal Lapor', 'submitted_at')->sortable(),


        ];
    }
}
