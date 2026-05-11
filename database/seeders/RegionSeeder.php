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
                'image' => 'images/regions/cairo.jfif',
            ],
            [
                'name' => 'Luxor',
                'description' => 'Home to Karnak Temple, Luxor Temple, and the Valley of the Kings.',
                'image' => 'images/regions/luxor.jfif',
            ],
            [
                'name' => 'Aswan',
                'description' => 'Southern Egypt destination with Nile scenery and Nubian heritage.',
                'image' => 'images/regions/aswan.jpg',
            ],
            [
                'name' => 'Alexandria',
                'description' => 'Mediterranean city with Greco-Roman landmarks and coastal attractions.',
                'image' => 'images/regions/alexandria.jpg',
            ],
            [
                'name' => 'Red Sea',
                'description' => 'Egypt\'s premier coastal destination with world-class diving, pristine beaches, and modern resorts.',
                'image' => 'images/regions/red-sea.jpg',
            ],
            [
                'name' => 'South Sinai',
                'description' => 'A coastal governorate that includes major beach cities like Sharm El Sheikh and Dahab.',
                'image' => 'images/regions/red-sea.jpg',
            ],
            [
                'name' => 'Matrouh',
                'description' => 'A Mediterranean coastal governorate known for beach cities such as Marsa Matrouh.',
                'image' => 'images/regions/alexandria-1.jpg',
            ],
        ];

        foreach ($items as $item) {
            Region::query()->firstOrCreate(
                ['name' => $item['name']],
                $item
            );
        }
    }
}