<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\JobApplication\Pages;

use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\File;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Textarea;
use MoonShine\UI\Fields\Preview;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use App\MoonShine\Resources\JobApplication\JobApplicationResource;
use MoonShine\Support\ListOf;
use Throwable;


/**
 * @extends DetailPage<JobApplicationResource>
 */
class JobApplicationDetailPage extends DetailPage
{
    /**
     * @return list<FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            ID::make()->sortable(),
            BelongsTo::make('Lowongan', 'jobVacancy', 'title', \App\MoonShine\Resources\JobVacancy\JobVacancyResource::class),
            BelongsTo::make('Pelamar', 'user', 'name', \App\MoonShine\Resources\User\UserResource::class),
            Preview::make('Profil & Keahlian Kandidat', 'user_id', function ($item) {
                if (!$item->user || !$item->user->alumniProfile) return 'Belum melengkapi profil.';
                $profile = $item->user->alumniProfile;
                
                $html = "";
                if ($profile->avatar_url) {
                    $html .= "<img src='/storage/{$profile->avatar_url}' style='width: 60px; height: 60px; border-radius: 50%; object-fit: cover; margin-bottom: 10px;'><br>";
                }
                $html .= "<strong>Keahlian:</strong> " . ($profile->skills ? implode(', ', $profile->skills) : 'Belum diisi') . "<br>";
                $html .= "<strong>Tentang Saya:</strong> " . ($profile->about_me ?? 'Belum diisi') . "<br>";
                
                $links = [];
                if ($profile->linkedin_url) $links[] = "<a href='{$profile->linkedin_url}' target='_blank'>LinkedIn</a>";
                if ($profile->portfolio_url) $links[] = "<a href='{$profile->portfolio_url}' target='_blank'>Portofolio</a>";
                
                if (!empty($links)) {
                    $html .= "<br>" . implode(' | ', $links);
                }
                
                return $html;
            }),
            File::make('CV Lampiran Loker', 'cv_url')->disk('public')->dir('job_applications/cv'),
            Textarea::make('Cover Letter', 'cover_letter')->nullable(),
            Select::make('Status', 'status')->options([
                'pending' => 'Menunggu Review',
                'reviewed' => 'Sedang Direview',
                'accepted' => 'Diterima',
                'rejected' => 'Ditolak'
            ]),
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
