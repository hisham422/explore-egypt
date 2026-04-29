<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class AttractionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'name' => $this->name,
            'description' => $this->description,
            'short_description' => Str::limit($this->description, 120),

            'image' => $this->image
                ? asset('storage/' . $this->image)
                : null,

            'location' => $this->location,

            'civilization' => CivilizationResource::make(
                $this->whenLoaded('civilization')
            ),

            'region' => RegionResource::make(
                $this->whenLoaded('region')
            ),

            'average_rating' => round($this->average_rating ?? 0, 1),
            'reviews_count' => $this->reviews_count ?? 0,
            'stars' => round($this->average_rating ?? 0, 1),

            'is_favorited' => (bool) ($this->is_favorited ?? false),

            'reviews' => ReviewResource::collection(
                $this->whenLoaded('reviews')
            ),
        ];
    }
}