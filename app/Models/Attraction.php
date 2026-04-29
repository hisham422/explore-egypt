<?php

namespace App\Models;

use App\Models\Concerns\HasStoredImage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Attraction extends Model
{
    use HasFactory, HasStoredImage;

    protected $fillable = [
        'name',
        'description',
        'image',
        'location',
        'civilization_id',
        'region_id',
    ];

    // 🔥 rename عشان consistency
    protected $appends = ['average_rating', 'reviews_count', 'favorites_count'];

    public function civilization(): BelongsTo
    {
        return $this->belongsTo(Civilization::class);
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(AttractionImage::class)->orderBy('sort_order')->orderBy('id');
    }

    public function usersWhoFavorited(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    // 🔥 optimized query
    public function scopeApiBase(Builder $query): Builder
    {
        return $query
            ->with(['civilization', 'region'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->withCount('favorites')
            ->latest();
    }

    public function scopeSearch(Builder $query, ?string $keyword): Builder
    {
        if (blank($keyword)) {
            return $query;
        }

        return $query->where(function ($q) use ($keyword) {
            $q->where('name', 'like', "%$keyword%")
              ->orWhere('description', 'like', "%$keyword%");
        });
    }

    public function scopeWithUserFavoriteState(Builder $query, int $userId): Builder
    {
        return $query->withExists([
            'favorites as is_favorited' => fn ($q) => $q->where('user_id', $userId),
        ])->withMax([
            'favorites as current_favorite_id' => fn ($q) => $q->where('user_id', $userId),
        ], 'id');
    }

    // 🔥 cleaner accessor
    public function getAverageRatingAttribute(): ?float
    {
        $rawAverage = $this->attributes['reviews_avg_rating'] ?? null;

        return $rawAverage
            ? round((float) $rawAverage, 1)
            : null;
    }

    public function getReviewsCountAttribute(): int
    {
        return (int) ($this->attributes['reviews_count'] ?? 0);
    }

    public function getFavoritesCountAttribute(): int
    {
        return (int) ($this->attributes['favorites_count'] ?? 0);
    }
}