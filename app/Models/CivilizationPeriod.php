<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CivilizationPeriod extends Model
{
    protected $table = 'civilization_periods';

    protected $fillable = [
        'civilization_id',
        'title',
        'start_year',
        'end_year',
        'description',
        'rulers',
        'sort_order',
    ];

    protected $casts = [
        'start_year' => 'integer',
        'end_year' => 'integer',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function civilization(): BelongsTo
    {
        return $this->belongsTo(Civilization::class);
    }

    public function attractions(): HasMany
    {
        return $this->hasMany(Attraction::class, 'civilization_period_id');
    }

    public function getFormattedYearRangeAttribute(): string
    {
        return $this->formatYear($this->start_year).' – '.$this->formatYear($this->end_year);
    }

    private function formatYear(int $year): string
    {
        return $this->isBceYear($year)
            ? abs($year).' BCE'
            : $year.' CE';
    }

    private function isBceYear(int $year): bool
    {
        if ($year < 0) {
            return true;
        }

        $civilizationName = $this->relationLoaded('civilization')
            ? $this->getRelation('civilization')?->name
            : null;

        $context = strtolower(trim($this->title.' '.($civilizationName ?? '')));

        return str_contains($context, 'ancient egypt')
            || str_contains($context, 'early dynastic')
            || str_contains($context, 'old kingdom')
            || str_contains($context, 'middle kingdom')
            || str_contains($context, 'new kingdom')
            || str_contains($context, 'late period')
            || str_contains($context, 'ptolemaic');
    }
}
