<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Event;

use Illuminate\Database\Eloquent\Model;
use App\Models\Event;
use App\MoonShine\Resources\Event\Pages\EventIndexPage;
use App\MoonShine\Resources\Event\Pages\EventFormPage;
use App\MoonShine\Resources\Event\Pages\EventDetailPage;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Contracts\Core\PageContract;

class EventResource extends ModelResource
{
    protected string $model = Event::class;

    protected string $title = 'Kegiatan';
    
    protected bool $withPolicy = false;

    public function rules(Model $item): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'event_type' => 'required|in:webinar,bootcamp,training,job_fair,other',
            'location_type' => 'required|in:online,offline,hybrid',
            'start_date' => 'required|date',
            'banner_url' => 'nullable|image|max:2048',
        ];
    }

    protected function beforeSave(Model $item): Model
    {
        if (!$item->exists) {
            $item->created_by = auth()->id();
        }

        if (empty($item->slug)) {
            $item->slug = \Illuminate\Support\Str::slug($item->title) . '-' . uniqid();
        }

        return $item;
    }

    protected function pages(): array
    {
        return [
            EventIndexPage::class,
            EventFormPage::class,
            EventDetailPage::class,
        ];
    }
}
