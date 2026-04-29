<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageManager
{
    public static function store(UploadedFile $file, string $directory, string $baseName): string
    {
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'jpg');
        $slug = Str::slug($baseName) ?: 'image';
        $candidate = $slug.'.'.$extension;
        $index = 1;

        while (Storage::disk('public')->exists($directory.'/'.$candidate)) {
            $candidate = $slug.'-'.$index.'.'.$extension;
            $index++;
        }

        return $file->storeAs($directory, $candidate, 'public');
    }

    public static function delete(?string $path): void
    {
        $normalizedPath = self::normalizePath($path);

        if (blank($normalizedPath) || Str::startsWith($normalizedPath, ['http://', 'https://'])) {
            return;
        }

        Storage::disk('public')->delete($normalizedPath);
    }

    public static function publicUrl(?string $path, string $fallbackLabel, string $placeholderSize = '800x500'): string
    {
        $normalizedPath = self::normalizePath($path);

        if (filled($normalizedPath)) {
            if (Str::startsWith($normalizedPath, ['http://', 'https://'])) {
                return $normalizedPath;
            }

            return asset('storage/'.$normalizedPath);
        }

        return self::placeholderUrl($fallbackLabel, $placeholderSize);
    }

    public static function normalizePath(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        $normalized = trim($path);

        if (Str::startsWith($normalized, ['/'])) {
            $normalized = ltrim($normalized, '/');
        }

        if (Str::startsWith($normalized, 'storage/')) {
            $normalized = Str::after($normalized, 'storage/');
        }

        if (Str::startsWith($normalized, '/storage/')) {
            $normalized = Str::after($normalized, '/storage/');
        }

        return $normalized !== '' ? $normalized : null;
    }

    public static function placeholderUrl(string $fallbackLabel, string $placeholderSize = '800x500'): string
    {
        [$width, $height] = array_pad(explode('x', strtolower($placeholderSize), 2), 2, '800');
        $width = (int) $width;
        $height = (int) $height;
        $innerWidth = max(0, $width - 48);
        $innerHeight = max(0, $height - 48);
        $label = e($fallbackLabel);

        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="{$width}" height="{$height}" viewBox="0 0 {$width} {$height}">
    <rect width="100%" height="100%" fill="#F1F3F7"/>
    <rect x="24" y="24" width="{$innerWidth}" height="{$innerHeight}" rx="28" fill="#FFFFFF" stroke="#D7DDE6" stroke-dasharray="10 10"/>
    <g fill="#1B2430" font-family="Arial, Helvetica, sans-serif" text-anchor="middle">
        <text x="50%" y="50%" font-size="28" font-weight="700" dominant-baseline="middle">{$label}</text>
        <text x="50%" y="62%" font-size="14" fill="#667085" dominant-baseline="middle">Image not available</text>
    </g>
</svg>
SVG;

        return 'data:image/svg+xml;charset=UTF-8,'.rawurlencode($svg);
    }
}
