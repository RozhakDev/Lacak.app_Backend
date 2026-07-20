<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\TracerSubmission;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
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

class TracerSubmissionResource extends ModelResource
{
    protected string $model = TracerSubmission::class;
    protected string $title = 'Laporan Tracer Study';



    protected function search(): array
    {
        return [
            'id',
            'status',
        ];
    }

    protected function pages(): array
    {
        return [
            TracerSubmissionIndexPage::class,
            TracerSubmissionFormPage::class,
            TracerSubmissionDetailPage::class,
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
        $fields = $this->myFields();

        $item = $this->getItem();
        if ($item) {
            if ($item->status === 'bekerja' && $item->work) {
                $fields[] = Text::make('Lokasi (Skala)', 'work.location_scale', fn($item) => str_replace('_', ' ', Str::title($item->work->location_scale)));
                $fields[] = Text::make('Lokasi (Negara)', 'work.location_country', fn($item) => str_replace('_', ' ', Str::title($item->work->location_country)));
                $fields[] = Text::make('Bidang Pekerjaan', 'work.field_of_work', fn($item) => $item->work->field_of_work);
                $fields[] = Text::make('Rentang Gaji', 'work.salary_range', fn($item) => str_replace('_', ' ', Str::title($item->work->salary_range)));
                $fields[] = Text::make('Nama Perusahaan', 'work.company_name', fn($item) => $item->work->company_name);
                $fields[] = Text::make('Posisi', 'work.position', fn($item) => $item->work->position);
                $fields[] = Text::make('Tanggal Mulai', 'work.start_date', fn($item) => $item->work->start_date);
                $fields[] = Text::make('Linearitas', 'work.is_linear', fn($item) => $item->work->is_linear ? 'Linear' : 'Tidak Linear');
            } elseif ($item->status === 'kuliah' && $item->study) {
                $fields[] = Text::make('Nama Universitas', 'study.university_name', fn($item) => $item->study->university_name);
                $fields[] = Text::make('Tanggal Masuk', 'study.enrollment_date', fn($item) => $item->study->enrollment_date);
                $fields[] = Text::make('Linearitas', 'study.is_linear', fn($item) => $item->study->is_linear ? 'Linear' : 'Tidak Linear');
            } elseif ($item->status === 'wirausaha' && $item->entrepreneur) {
                $fields[] = Text::make('Kepemilikan Usaha', 'entrepreneur.ownership_type', fn($item) => str_replace('_', ' ', Str::title($item->entrepreneur->ownership_type)));
                $fields[] = Text::make('Jumlah Karyawan', 'entrepreneur.employee_count', fn($item) => $item->entrepreneur->employee_count);
                $fields[] = Text::make('Rentang Omset Bulanan', 'entrepreneur.monthly_omset_range', fn($item) => str_replace('_', ' ', Str::title($item->entrepreneur->monthly_omset_range)));
                $fields[] = Text::make('Jenis Usaha', 'entrepreneur.business_type', fn($item) => $item->entrepreneur->business_type);
            }
        }

        return $fields;
    }

    private function myFields(): array
    {
        return [
            ID::make()->sortable(),
            BelongsTo::make('Alumni', 'alumniProfile', function($profile) {
                return $profile->user->name . ' (' . $profile->user->nisn . ')';
            }),
            Text::make('Status', 'status')
                ->badge(fn($status) => match ($status) {
                    'bekerja' => 'success',
                    'kuliah' => 'info',
                    'wirausaha' => 'warning',
                    default => 'gray'
                }),
            Text::make('Tanggal Lapor', 'submitted_at')->sortable(),
        ];
    }
}
