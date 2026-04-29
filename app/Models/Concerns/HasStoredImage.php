<?php

namespace App\Models\Concerns;

use App\Support\ImageManager;

trait HasStoredImage
{
    public function imageUrl(string $placeholderSize = '800x500'): string
    {
        $fallbackLabel = $this->name ?? $this->title ?? class_basename($this);

        return ImageManager::publicUrl($this->image, $fallbackLabel, $placeholderSize);
    }
}
