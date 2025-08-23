<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

trait HasOwner
{
    protected static function bootHasOwner()
    {
        static::creating(function ($model) {
            if (Auth::check() && !$model->user_id) {
                $model->user_id = Auth::id();
            }
        });

        static::addGlobalScope('owner', function (Builder $builder) {
            $user = Auth::user();
            if (Auth::check() && $user && $user->isAdmin && !$user->isAdmin()) {
                $builder->where('user_id', Auth::id());
            }
        });
    }

    public function scopeOwnedBy(Builder $query, $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeSharedWithMe(Builder $query): Builder
    {
        if (Auth::check()) {
            return $query->whereHas('sharedUsers', function ($q) {
                $q->where('user_id', Auth::id());
            });
        }
        
        return $query;
    }

    public function isOwnedBy($userId): bool
    {
        return $this->user_id == $userId;
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function authorizeOwnerAccess()
    {
        if (!$this->isOwnedBy(Auth::id())) {
            abort(403, 'Você não tem permissão para acessar este recurso');
        }
    }
}