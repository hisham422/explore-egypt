<?php

namespace Database\Seeders;

use App\Models\Attraction;
use App\Models\Civilization;
use App\Models\Region;
use Illuminate\Database\Seeder;

class AttractionSeeder extends Seeder
{
    public function run(): void
    {
        $ancient = Civilization::query()->where('name', 'Ancient Egypt')->firstOrFail();
        $islamic = Civilization::query()->where('name', 'Islamic')->firstOrFail();
        $coptic = Civilization::query()->where('name', 'Coptic')->firstOrFail();
        $grecoRoman = Civilization::query()->where('name', 'Greco-Roman')->firstOrFail();

        $cairo = Region::query()->where('name', 'Cairo')->firstOrFail();
        $luxor = Region::query()->where('name', 'Luxor')->firstOrFail();
        $aswan = Region::query()->where('name', 'Aswan')->firstOrFail();
        $alexandria = Region::query()->where('name', 'Alexandria')->firstOrFail();

        $items = [
            [
                'name' => 'Pyramids of Giza',
                'description' => 'The iconic complex including the Great Pyramid and Sphinx.',
                'image' => 'attractions/pyramids-giza.jpg',
                'location' => 'Giza Plateau, Cairo',
                'civilization_id' => $ancient->id,
                'region_id' => $cairo->id,
            ],
            [
                'name' => 'Karnak Temple',
                'description' => 'A vast temple complex built over many generations of pharaohs.',
                'image' => 'attractions/karnak.jpg',
                'location' => 'Luxor',
                'civilization_id' => $ancient->id,
                'region_id' => $luxor->id,
            ],
            [
                'name' => 'Abu Simbel Temples',
                'description' => 'Rock temples commissioned by Ramesses II near Lake Nasser.',
                'image' => 'attractions/abu-simbel.jpg',
                'location' => 'Aswan',
                'civilization_id' => $ancient->id,
                'region_id' => $aswan->id,
            ],
            [
                'name' => 'Citadel of Cairo',
                'description' => 'Historic Islamic fortress with panoramic city views.',
                'image' => 'attractions/citadel-cairo.jpg',
                'location' => 'Cairo',
                'civilization_id' => $islamic->id,
                'region_id' => $cairo->id,
            ],
            [
                'name' => 'Hanging Church',
                'description' => 'One of the oldest Coptic churches in Old Cairo.',
                'image' => 'attractions/hanging-church.jpg',
                'location' => 'Old Cairo',
                'civilization_id' => $coptic->id,
                'region_id' => $cairo->id,
            ],
            [
                'name' => 'Catacombs of Kom El Shoqafa',
                'description' => 'A major Greco-Roman burial site in Alexandria.',
                'image' => 'attractions/kom-el-shoqafa.jpg',
                'location' => 'Alexandria',
                'civilization_id' => $grecoRoman->id,
                'region_id' => $alexandria->id,
            ],
        ];

        foreach ($items as $item) {
            Attraction::query()->updateOrCreate(
                ['name' => $item['name']],
                $item
            );
        }
    }
}