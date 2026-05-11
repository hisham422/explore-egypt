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
                'image' => 'images/civilizations/ancient-egypt.avif',
                'hero_video_url' => 'videos/civilizations/ancient-egypt.mp4',
            ],
            [
                'name' => 'Islamic',
                'description' => 'A rich heritage of mosques, citadels, and medieval urban history.',
                'image' => 'images/civilizations/islamic.png',
                'hero_video_url' => 'videos/civilizations/islamic.mp4',
            ],
            [
                'name' => 'Coptic',
                'description' => 'Historic churches, monasteries, and Christian heritage across Egypt.',
                'image' => 'images/civilizations/coptic.jpg',
                'hero_video_url' => 'videos/civilizations/coptic.mp4',
            ],
            [
                'name' => 'Greco-Roman',
                'description' => 'Classical influences in architecture, theaters, and archaeological sites.',
                'image' => 'images/civilizations/greco-roman.jpg',
                'hero_video_url' => 'videos/civilizations/greco-roman.mp4',
            ],
            [
                'name' => 'Modern Civilization',
                'description' => 'Contemporary Egypt shaped by modern architecture, urban culture, technological growth, and national development.',
                'image' => 'images/civilizations/modern-civilization-1.png',
                'hero_video_url' => 'videos/civilizations/modern-civilization-1.mp4',
            ],
        ];

        foreach ($items as $item) {
            Civilization::query()->firstOrCreate(
                ['name' => $item['name']],
                $item
            );
        }
    }
}