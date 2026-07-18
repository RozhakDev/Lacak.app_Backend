<?php

namespace App\Services;

use App\Models\JobVacancy;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class JobVacancyService
{
    public function getActiveJobs(?string $search, int $perPage = 10): LengthAwarePaginator
    {
        $query = JobVacancy::with('creator')
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>=', now()->toDateString());
            });

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%");
            });
        }

        return $query->latest()->paginate($perPage);
    }

     public function getJobDetail(int $id): JobVacancy
    {
        $job = JobVacancy::with('creator')
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>=', now()->toDateString());
            })
            ->find($id);

        if (!$job) {
            throw new ModelNotFoundException('Lowongan tidak ditemukan atau sudah ditutup.');
        }

        return $job;
    }
}