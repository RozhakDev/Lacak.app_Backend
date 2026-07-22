<?php

namespace App\Services;

use App\Models\JobVacancy;
use App\Models\JobApplication;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\UploadedFile;
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

        if (auth()->check()) {
            $query->withExists(['jobApplications as is_applied' => function ($q) {
                $q->where('user_id', auth()->id());
            }]);
        }

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
        $jobQuery = JobVacancy::with('creator')
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>=', now()->toDateString());
            });

        if (auth()->check()) {
            $jobQuery->withExists(['jobApplications as is_applied' => function ($q) {
                $q->where('user_id', auth()->id());
            }]);
        }

        $job = $jobQuery->find($id);

        if (!$job) {
            throw new ModelNotFoundException('Lowongan tidak ditemukan atau sudah ditutup.');
        }

        return $job;
    }

    public function applyToJob(int $jobId, int $userId, UploadedFile $cvFile, ?string $coverLetter): JobApplication
    {
        $job = $this->getJobDetail($jobId);

        $existingApp = JobApplication::where('job_vacancy_id', $jobId)
            ->where('user_id', $userId)
            ->first();

        if ($existingApp) {
            throw new Exception('Anda sudah melamar pekerjaan ini sebelumnya.');
        }

        $cvPath = $cvFile->store('job_applications/cv', 'public');

        return JobApplication::create([
            'job_vacancy_id' => $jobId,
            'user_id' => $userId,
            'cv_url' => $cvPath,
            'cover_letter' => $coverLetter,
            'status' => 'pending'
        ]);
    }

    public function getMyApplications(int $userId, int $perPage = 10): LengthAwarePaginator
    {
        return JobApplication::with('jobVacancy')
            ->where('user_id', $userId)
            ->latest()
            ->paginate($perPage);
    }
}