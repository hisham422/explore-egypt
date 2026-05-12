<?php

namespace Database\Seeders;

use App\Models\Attraction;
use Illuminate\Database\Seeder;

class AttractionCoordinatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Real or near-real coordinates for attractions that should appear on the map.
        $attractions = [
            'Pyramids of Giza' => ['lat' => 29.9789, 'lng' => 31.1344],
            'Great Sphinx' => ['lat' => 29.9756, 'lng' => 31.1379],
            'Egyptian Museum' => ['lat' => 30.0479, 'lng' => 31.2352],
            'Khan el-Khalili' => ['lat' => 30.0634, 'lng' => 31.2590],
            'Karnak Temple' => ['lat' => 25.7397, 'lng' => 32.6597],
            'Luxor Temple' => ['lat' => 25.7369, 'lng' => 32.6395],
            'Valley of the Kings' => ['lat' => 25.7400, 'lng' => 32.5900],
            'Temple of Hatshepsut' => ['lat' => 25.7397, 'lng' => 32.6100],
            'Abu Simbel' => ['lat' => 22.3457, 'lng' => 31.6085],
            'Aswan High Dam' => ['lat' => 23.9631, 'lng' => 32.8794],
            'Philae Temple' => ['lat' => 24.0154, 'lng' => 32.8851],
            'Colossi of Memnon' => ['lat' => 25.7456, 'lng' => 32.5911],
            'Cairo Citadel' => ['lat' => 30.0278, 'lng' => 31.2651],
            'Al-Azhar Mosque' => ['lat' => 30.0597, 'lng' => 31.2628],
            'Saqqara' => ['lat' => 29.8695, 'lng' => 31.4159],
            'Dahshur' => ['lat' => 29.8061, 'lng' => 31.2056],
            'Hurghada Beaches' => ['lat' => 27.2579, 'lng' => 33.8116],
            'Sharm El Sheikh' => ['lat' => 27.9158, 'lng' => 34.3299],
            'Dahab Blue Lagoon' => ['lat' => 28.5091, 'lng' => 34.5136],
            'Alexandria Corniche' => ['lat' => 31.2156, 'lng' => 29.9553],
            'El Gouna Marina' => ['lat' => 27.3942, 'lng' => 33.6764],
            'North Coast Beaches' => ['lat' => 31.0459, 'lng' => 28.4801],
            'Nile Felucca Ride' => ['lat' => 30.0479, 'lng' => 31.2337],
            'Khan El Khalili Walking Tour' => ['lat' => 30.0475, 'lng' => 31.2626],
            'Egyptian Museum' => ['lat' => 30.0478, 'lng' => 31.2336],
            'Hot Air Balloon Luxor' => ['lat' => 25.7369, 'lng' => 32.6395],
            'Valley of the Kings' => ['lat' => 25.7402, 'lng' => 32.6014],
            'Luxor Temple' => ['lat' => 25.6997, 'lng' => 32.6396],
            'Nubian Village Visit' => ['lat' => 24.0856, 'lng' => 32.8976],
            'Aswan Felucca Sunset Sail' => ['lat' => 24.0889, 'lng' => 32.8998],
            'Philae Night Show' => ['lat' => 24.0238, 'lng' => 32.8854],
            'Qaitbay Citadel' => ['lat' => 31.2156, 'lng' => 29.8851],
            'Alexandria Corniche' => ['lat' => 31.2156, 'lng' => 29.9553],
            'Hurghada Snorkeling Trip' => ['lat' => 27.2579, 'lng' => 33.8116],
            'Hurghada Scuba Diving' => ['lat' => 27.2579, 'lng' => 33.8116],
            'El Gouna Kitesurfing' => ['lat' => 27.3922, 'lng' => 33.6772],
            'Ras Mohamed Boat Trip' => ['lat' => 27.7289, 'lng' => 34.2533],
            'Dahab' => ['lat' => 28.5000, 'lng' => 34.5130],
            'Cleopatra Beach' => ['lat' => 31.3547, 'lng' => 27.2468],
            'Ageeba Beach' => ['lat' => 31.3089, 'lng' => 27.2044],
            'Jabal Musa' => ['lat' => 28.5390, 'lng' => 33.9738],
            'Saint Catherine' => ['lat' => 28.5551, 'lng' => 33.9750],
            'Siwa' => ['lat' => 29.2030, 'lng' => 25.5196],
            'Al-Muizz Street' => ['lat' => 30.0577, 'lng' => 31.2621],
        ];

        foreach ($attractions as $name => $coords) {
            Attraction::where('name', 'like', '%' . $name . '%')
                ->where(function ($query) {
                    $query->whereNull('lat')->orWhereNull('lng');
                })
                ->update([
                    'lat' => $coords['lat'],
                    'lng' => $coords['lng'],
                ]);
        }

        // Also update any attractions with NULL lat/lng by location hints.
        $locationHints = [
            ['search' => 'pyramid', 'lat' => 29.9789, 'lng' => 31.1344],
            ['search' => 'cairo', 'lat' => 30.0444, 'lng' => 31.2357],
            ['search' => 'luxor', 'lat' => 25.7369, 'lng' => 32.6395],
            ['search' => 'aswan', 'lat' => 24.0889, 'lng' => 32.8998],
            ['search' => 'temple', 'lat' => 25.7397, 'lng' => 32.6597],
            ['search' => 'alexandria', 'lat' => 31.2001, 'lng' => 29.9187],
            ['search' => 'hurghada', 'lat' => 27.2579, 'lng' => 33.8116],
            ['search' => 'gouna', 'lat' => 27.3922, 'lng' => 33.6772],
            ['search' => 'dahab', 'lat' => 28.5000, 'lng' => 34.5130],
            ['search' => 'matrouh', 'lat' => 31.3547, 'lng' => 27.2468],
            ['search' => 'sinai', 'lat' => 28.5390, 'lng' => 33.9738],
            ['search' => 'siwa', 'lat' => 29.2030, 'lng' => 25.5196],
        ];

        foreach ($locationHints as $hint) {
            Attraction::where(function ($query) {
                    $query->whereNull('lat')->orWhereNull('lng');
                })
                ->where('name', 'like', '%' . $hint['search'] . '%')
                ->update([
                    'lat' => $hint['lat'],
                    'lng' => $hint['lng'],
                ]);
        }

        $this->command->info('Attraction coordinates seeded successfully.');
    }
}
