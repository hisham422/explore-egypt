<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Seeder;

class RegionSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'name' => 'Cairo',
                'description' => 'Egypt\'s capital, known for Islamic architecture and major museums.',
                'image' => 'regions/cairo.jpg',
            ],
            [
                'name' => 'Luxor',
                'description' => 'Home to Karnak Temple, Luxor Temple, and the Valley of the Kings.',
                'image' => 'regions/luxor.jpg',
            ],
            [
                'name' => 'Aswan',
                'description' => 'Southern Egypt destination with Nile scenery and Nubian heritage.',
                'image' => 'regions/aswan.jpg',
            ],
            [
                'name' => 'Alexandria',
                'description' => 'Mediterranean city with Greco-Roman landmarks and coastal attractions.',
                'image' => 'regions/alexandria.jpg',
            ],
        ];

        foreach ($items as $item) {
            Region::query()->updateOrCreate(
                ['name' => $item['name']],
                $item
            );
        }
    }
}