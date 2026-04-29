<?php

namespace Database\Seeders;

use App\Models\Civilization;
use Illuminate\Database\Seeder;

class CivilizationSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'name' => 'Ancient Egypt',
                'description' => 'The civilization of the pharaohs, pyramids, temples, and enduring monuments.',
                'image' => 'civilizations/ancient-egypt.jpg',
            ],
            [
                'name' => 'Islamic',
                'description' => 'A rich heritage of mosques, citadels, and medieval urban history.',
                'image' => 'civilizations/islamic.jpg',
            ],
            [
                'name' => 'Coptic',
                'description' => 'Historic churches, monasteries, and Christian heritage across Egypt.',
                'image' => 'civilizations/coptic.jpg',
            ],
            [
                'name' => 'Greco-Roman',
                'description' => 'Classical influences in architecture, theaters, and archaeological sites.',
                'image' => 'civilizations/greco-roman.jpg',
            ],
        ];

        foreach ($items as $item) {
            Civilization::query()->updateOrCreate(
                ['name' => $item['name']],
                $item
            );
        }
    }
}