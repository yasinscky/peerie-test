<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CabinetNotification extends Model
{
    protected $table = 'notifications';

    protected $fillable = [
        'user_id',
        'language',
        'title',
        'body',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reads(): HasMany
    {
        return $this->hasMany(NotificationRead::class, 'notification_id');
    }

    public function scopeVisibleTo(Builder $query, User $user, string $language): Builder
    {
        return $query
            ->where('language', $language)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->where(function (Builder $query) use ($user) {
                $query->whereNull('user_id')->orWhere('user_id', $user->id);
            });
    }
}
