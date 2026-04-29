<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class CivilizationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'name' => $this->name,
            'description' => $this->description,
            'short_description' => Str::limit($this->description, 100),

            'image' => $this->image
                ? asset('storage/' . $this->image)
                : null,
        ];
    }
}