<?php

namespace Database\Seeders;

use App\Models\Attraction;
use App\Models\AttractionImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AttractionMediaSeeder extends Seeder
{
    private const IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp', 'avif', 'jfif'];
    private const VIDEO_EXTENSIONS = ['mp4', 'mov', 'webm', 'ogg'];

    public function run(): void
    {
        $disk = Storage::disk('public');
        $allFiles = collect($disk->files('images/attractions'));

        $mediaFiles = $allFiles->filter(function (string $file): bool {
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            return in_array($ext, array_merge(self::IMAGE_EXTENSIONS, self::VIDEO_EXTENSIONS), true);
        })->values();

        $attractions = Attraction::query()->get();

        foreach ($attractions as $attraction) {
            $slugCandidates = $this->buildSlugCandidates($attraction);

            $mainImage = $this->findMainImage($mediaFiles, $slugCandidates);
            $currentImage = (string) ($attraction->image ?? '');
            $hasValidMainImage = filled($currentImage)
                && ! Str::startsWith($currentImage, ['http://', 'https://'])
                && $disk->exists($currentImage);

            if (! $hasValidMainImage && filled($mainImage)) {
                $attraction->forceFill(['image' => $mainImage])->save();
            }

            if ($attraction->images()->exists()) {
                // Keep admin-curated galleries intact.
                continue;
            }

            $gallery = $this->findGalleryMedia($mediaFiles, $slugCandidates);

            if ($gallery->isEmpty()) {
                continue;
            }

            foreach ($gallery->values() as $index => $file) {
                $type = $this->mediaType($file);

                AttractionImage::query()->create([
                    'attraction_id' => $attraction->id,
                    'image' => $file,
                    'type' => $type,
                    'sort_order' => $index + 1,
                ]);
            }
        }
    }

    private function findMainImage($mediaFiles, $slugCandidates): ?string
    {
        foreach ($slugCandidates as $slug) {
            foreach (self::IMAGE_EXTENSIONS as $ext) {
                $target = 'images/attractions/'.$slug.'.'.$ext;
                if ($mediaFiles->contains($target)) {
                    return $target;
                }
            }
        }

        // If no dedicated main image exists, use first gallery image as main.
        $gallery = $this->findGalleryMedia($mediaFiles, $slugCandidates)
            ->first(fn (string $file): bool => $this->mediaType($file) === 'image');

        if (is_string($gallery) && $gallery !== '') {
            return $gallery;
        }

        return null;
    }

    private function findGalleryMedia($mediaFiles, $slugCandidates)
    {
        $matched = collect();

        foreach ($slugCandidates as $slug) {
            $prefix = 'images/attractions/'.$slug.'-gallery-';
            $group = $mediaFiles->filter(fn (string $file): bool => Str::startsWith($file, $prefix));
            if ($group->isNotEmpty()) {
                $matched = $matched->merge($group);
            }
        }

        return $matched
            ->unique()
            ->sortBy(function (string $file): int {
                if (preg_match('/-gallery-(\d+)/', $file, $matches) === 1) {
                    return (int) $matches[1];
                }

                return 999;
            })
            ->values();
    }

    private function buildSlugCandidates(Attraction $attraction)
    {
        $baseCandidates = [Str::slug((string) $attraction->name)];

        if (filled($attraction->image) && !Str::startsWith((string) $attraction->image, ['http://', 'https://'])) {
            $pathBase = pathinfo((string) $attraction->image, PATHINFO_FILENAME);
            $pathBase = preg_replace('/-gallery-\d+$/', '', (string) $pathBase) ?? $pathBase;
            $baseCandidates[] = $pathBase;
        }

        if (filled($attraction->location)) {
            $baseCandidates[] = Str::slug((string) $attraction->location);
        }

        return collect($baseCandidates)
            ->filter(fn (?string $value): bool => filled($value))
            ->map(fn (string $value): string => trim($value))
            ->flatMap(function (string $value): array {
                $withoutThe = Str::startsWith($value, 'the-') ? Str::after($value, 'the-') : $value;

                return [
                    $value,
                    $withoutThe,
                    'the-'.$withoutThe,
                ];
            })
            ->map(fn (string $value): string => trim($value, '-'))
            ->filter(fn (string $value): bool => $value !== '')
            ->unique()
            ->values();
    }

    private function mediaType(string $file): string
    {
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        return in_array($ext, self::VIDEO_EXTENSIONS, true) ? 'video' : 'image';
    }
}
