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
            [
                'name' => 'Hurghada',
                'description' => 'Red Sea destination known for coral reefs, beach resorts, and water sports.',
                'image' => 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&fit=crop&w=1400&q=80',
            ],
            [
                'name' => 'Sharm El Sheikh',
                'description' => 'A vibrant resort city with diving sites and scenic beaches on the Sinai coast.',
                'image' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1400&q=80',
            ],
            [
                'name' => 'Dahab',
                'description' => 'A laid-back coastal town popular for diving, snorkeling, and relaxed beach life.',
                'image' => 'https://images.unsplash.com/photo-1473116763249-2faaef81ccda?auto=format&fit=crop&w=1400&q=80',
            ],
            [
                'name' => 'El Gouna',
                'description' => 'A modern lagoon town on the Red Sea featuring marinas, beaches, and upscale tourism.',
                'image' => 'https://images.unsplash.com/photo-1500375592092-40eb2168fd21?auto=format&fit=crop&w=1400&q=80',
            ],
            [
                'name' => 'North Coast',
                'description' => 'Egypt\'s Mediterranean summer destination with long sandy beaches and seaside compounds.',
                'image' => 'https://images.unsplash.com/photo-1501959915551-4e8d30928317?auto=format&fit=crop&w=1400&q=80',
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