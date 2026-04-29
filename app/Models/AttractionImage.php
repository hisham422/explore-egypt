<?php

namespace App\Models;

use App\Models\Concerns\HasStoredImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AttractionImage extends Model
{
    use HasFactory, HasStoredImage;

    protected $fillable = [
        'attraction_id',
        'image',
        'type',
        'sort_order',
    ];

    public function attraction(): BelongsTo
    {
        return $this->belongsTo(Attraction::class);
    }

    public function getFallbackLabelAttribute(): string
    {
        return $this->attraction?->name ?? 'Attraction media';
    }

    public function isVideo(): bool
    {
        if ($this->type === 'video') {
            return true;
        }

        // Fallback for legacy rows where type was missing/wrong.
        return Str::endsWith(strtolower((string) $this->image), '.mp4');
    }

    public function isImage(): bool
    {
        return ! $this->isVideo();
    }

    public function videoUrl(): string
    {
        if (!$this->image || !$this->isVideo()) {
            return '';
        }

        if (Str::startsWith($this->image, ['http://', 'https://'])) {
            return $this->image;
        }

        return asset('storage/' . $this->image);
    }
}
