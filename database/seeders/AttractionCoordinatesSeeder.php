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
        // Real coordinates for famous Egyptian attractions
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
        ];

        foreach ($attractions as $name => $coords) {
            Attraction::where('name', 'like', '%' . $name . '%')
                ->whereColumn('created_at', 'updated_at')
                ->where(function ($query) {
                    $query->whereNull('lat')->orWhereNull('lng');
                })
                ->update([
                    'lat' => $coords['lat'],
                    'lng' => $coords['lng'],
                ]);
        }

        // Also update any attractions with NULL lat/lng by location hints
        $locationHints = [
            ['search' => 'pyramid', 'lat' => 29.9789, 'lng' => 31.1344],
            ['search' => 'cairo', 'lat' => 30.0444, 'lng' => 31.2357],
            ['search' => 'luxor', 'lat' => 25.7369, 'lng' => 32.6395],
            ['search' => 'aswan', 'lat' => 24.0889, 'lng' => 32.8998],
            ['search' => 'temple', 'lat' => 25.7397, 'lng' => 32.6597],
        ];

        foreach ($locationHints as $hint) {
            Attraction::whereRaw('lat is null or lng is null')
                ->whereColumn('created_at', 'updated_at')
                ->where('name', 'like', '%' . $hint['search'] . '%')
                ->update([
                    'lat' => $hint['lat'],
                    'lng' => $hint['lng'],
                ]);
        }

        $this->command->info('Attraction coordinates seeded successfully.');
    }
}
