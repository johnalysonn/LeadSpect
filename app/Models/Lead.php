<?php

namespace App\Models;

use App\Enums\LeadStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id', 'osm_id', 'name', 'category', 'address', 'city', 'neighborhood',
    'postal_code', 'latitude', 'longitude', 'phone', 'whatsapp', 'email',
    'website', 'instagram', 'facebook', 'linkedin', 'rating', 'review_count',
    'status', 'is_favorite', 'is_open_now', 'opening_hours', 'enriched_at', 'metadata'
])]
class Lead extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'rating' => 'decimal:2',
            'review_count' => 'integer',
            'status' => LeadStatus::class,
            'is_favorite' => 'boolean',
            'is_open_now' => 'boolean',
            'enriched_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(LeadStatusHistory::class)->latest();
    }

    public function notes(): HasMany
    {
        return $this->hasMany(LeadNote::class)->latest();
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }
}
