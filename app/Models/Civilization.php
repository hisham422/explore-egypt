<?php

namespace App\Models;

use App\Models\Concerns\HasStoredImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Civilization extends Model
{
    use HasFactory, HasStoredImage;

    protected $fillable = [
        'name',
        'description',
        'image',
        'hero_video_url',
    ];

    public function attractions(): HasMany
    {
        return $this->hasMany(Attraction::class);
    }

    public function periods(): HasMany
    {
        return $this->hasMany(CivilizationPeriod::class)->orderBy('start_year');
    }
}