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
        $hurghada = Region::query()->where('name', 'Hurghada')->firstOrFail();
        $sharm = Region::query()->where('name', 'Sharm El Sheikh')->firstOrFail();
        $dahab = Region::query()->where('name', 'Dahab')->firstOrFail();
        $elGouna = Region::query()->where('name', 'El Gouna')->firstOrFail();
        $northCoast = Region::query()->where('name', 'North Coast')->firstOrFail();
        $modern = Civilization::query()->where('name', 'Modern Civilization')->firstOrFail();

        $items = [
            [
                'name' => 'Pyramids of Giza',
                'description' => 'The iconic complex including the Great Pyramid and Sphinx.',
                'image' => 'attractions/pyramids-giza.jpg',
                'type' => 'historical',
                'location' => 'Giza Plateau, Cairo',
                'civilization_id' => $ancient->id,
                'region_id' => $cairo->id,
            ],
            [
                'name' => 'Karnak Temple',
                'description' => 'A vast temple complex built over many generations of pharaohs.',
                'image' => 'attractions/karnak.jpg',
                'type' => 'historical',
                'location' => 'Luxor',
                'civilization_id' => $ancient->id,
                'region_id' => $luxor->id,
            ],
            [
                'name' => 'Abu Simbel Temples',
                'description' => 'Rock temples commissioned by Ramesses II near Lake Nasser.',
                'image' => 'attractions/abu-simbel.jpg',
                'type' => 'historical',
                'location' => 'Aswan',
                'civilization_id' => $ancient->id,
                'region_id' => $aswan->id,
            ],
            [
                'name' => 'Citadel of Cairo',
                'description' => 'Historic Islamic fortress with panoramic city views.',
                'image' => 'attractions/citadel-cairo.jpg',
                'type' => 'historical',
                'location' => 'Cairo',
                'civilization_id' => $islamic->id,
                'region_id' => $cairo->id,
            ],
            [
                'name' => 'Hanging Church',
                'description' => 'One of the oldest Coptic churches in Old Cairo.',
                'image' => 'attractions/hanging-church.jpg',
                'type' => 'historical',
                'location' => 'Old Cairo',
                'civilization_id' => $coptic->id,
                'region_id' => $cairo->id,
            ],
            [
                'name' => 'Catacombs of Kom El Shoqafa',
                'description' => 'A major Greco-Roman burial site in Alexandria.',
                'image' => 'attractions/kom-el-shoqafa.jpg',
                'type' => 'historical',
                'location' => 'Alexandria',
                'civilization_id' => $grecoRoman->id,
                'region_id' => $alexandria->id,
            ],
            [
                'name' => 'Hurghada Beaches',
                'description' => 'Crystal-clear Red Sea waters, vibrant reefs, and lively beachfront resorts.',
                'image' => 'https://images.unsplash.com/photo-1559827260-dc66d52bef19?auto=format&fit=crop&w=1400&q=80',
                'type' => 'beach',
                'location' => 'Hurghada',
                'civilization_id' => $modern->id,
                'region_id' => $hurghada->id,
            ],
            [
                'name' => 'Sharm El Sheikh',
                'description' => 'World-class diving spots, coral-rich bays, and sunny coastal promenades.',
                'image' => 'https://images.unsplash.com/photo-1501785888041-af3ef285b470?auto=format&fit=crop&w=1400&q=80',
                'type' => 'beach',
                'location' => 'Sharm El Sheikh',
                'civilization_id' => $modern->id,
                'region_id' => $sharm->id,
            ],
            [
                'name' => 'Dahab Blue Lagoon',
                'description' => 'Relaxed beach atmosphere, turquoise waters, and adventure-friendly shoreline.',
                'image' => 'https://images.unsplash.com/photo-1493558103817-58b2924bce98?auto=format&fit=crop&w=1400&q=80',
                'type' => 'beach',
                'location' => 'Dahab',
                'civilization_id' => $modern->id,
                'region_id' => $dahab->id,
            ],
            [
                'name' => 'Alexandria Corniche',
                'description' => 'Historic Mediterranean waterfront with sea views, cafes, and city landmarks.',
                'image' => 'https://images.unsplash.com/photo-1473116763249-2faaef81ccda?auto=format&fit=crop&w=1400&q=80',
                'type' => 'coastal',
                'location' => 'Alexandria',
                'civilization_id' => $modern->id,
                'region_id' => $alexandria->id,
            ],
            [
                'name' => 'El Gouna Marina',
                'description' => 'A modern coastal lifestyle destination with marinas, lagoons, and beach clubs.',
                'image' => 'https://images.unsplash.com/photo-1454391304352-2bf4678b1a7a?auto=format&fit=crop&w=1400&q=80',
                'type' => 'coastal',
                'location' => 'El Gouna',
                'civilization_id' => $modern->id,
                'region_id' => $elGouna->id,
            ],
            [
                'name' => 'North Coast Beaches',
                'description' => 'Long white-sand shoreline and resort towns ideal for summer escapes.',
                'image' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1400&q=80',
                'type' => 'coastal',
                'location' => 'North Coast',
                'civilization_id' => $modern->id,
                'region_id' => $northCoast->id,
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