<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class SchoolScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        if (Auth::hasUser()) {
            $user = Auth::user();
            if (!$user->hasRole('Super Admin')) {
                $builder->where(function ($query) use ($model, $user) {
                    $query->where($model->getTable() . '.school_id', $user->school_id)
                          ->orWhereNull($model->getTable() . '.school_id');
                });
            }
        }
    }
}
