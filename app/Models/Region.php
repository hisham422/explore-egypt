<?php

namespace App\Models;

use App\Models\Concerns\HasStoredImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Region extends Model
{
    use HasFactory, HasStoredImage;

    protected $fillable = [
        'name',
        'description',
        'image',
    ];

    public function attractions(): HasMany
    {
        return $this->hasMany(Attraction::class);
    }
}