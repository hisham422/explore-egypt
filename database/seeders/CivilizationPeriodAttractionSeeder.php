<?php

namespace Database\Seeders;

use App\Models\Attraction;
use App\Models\Civilization;
use App\Models\CivilizationPeriod;
use App\Models\Region;
use Illuminate\Database\Seeder;

class CivilizationPeriodAttractionSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['seed_key' => 'mastaba-tombs-of-saqqara', 'name' => 'Mastaba Tombs of Saqqara', 'civilization' => 'Ancient Egypt', 'period' => 'Early Dynastic Period', 'region' => 'Cairo', 'location' => 'Saqqara', 'description' => 'Early royal and noble tombs that show the transition toward monumental stone funerary architecture.'],
            ['seed_key' => 'pyramids-of-giza', 'name' => 'Pyramids of Giza', 'civilization' => 'Ancient Egypt', 'period' => 'Old Kingdom', 'region' => 'Cairo', 'location' => 'Giza Plateau, Cairo', 'description' => 'The iconic complex including the Great Pyramid and Sphinx.'],
            ['seed_key' => 'el-lisht-pyramid-complex', 'name' => 'El-Lisht Pyramid Complex', 'civilization' => 'Ancient Egypt', 'period' => 'Middle Kingdom', 'region' => 'Cairo', 'location' => 'El-Lisht, Cairo', 'description' => 'A royal pyramid site associated with the rise of the Middle Kingdom and renewed centralized power.'],
            ['seed_key' => 'karnak-temple', 'name' => 'Karnak Temple', 'civilization' => 'Ancient Egypt', 'period' => 'New Kingdom', 'region' => 'Luxor', 'location' => 'Luxor', 'description' => 'A vast temple complex built over many generations of pharaohs.'],
            ['seed_key' => 'saqqara-serapeum', 'name' => 'Saqqara Serapeum', 'civilization' => 'Ancient Egypt', 'period' => 'Late Period', 'region' => 'Cairo', 'location' => 'Saqqara', 'description' => 'The burial place of the Apis bulls, reflecting the religious continuity of late ancient Egypt.'],

            ['seed_key' => 'mosque-of-amr-ibn-al-as', 'name' => 'Mosque of Amr ibn al-As', 'civilization' => 'Islamic', 'period' => 'Umayyad Period', 'region' => 'Cairo', 'location' => 'Old Cairo', 'description' => 'The first mosque built in Egypt and one of the oldest Islamic monuments in Africa.'],
            ['seed_key' => 'historic-fustat-ruins', 'name' => 'Historic Fustat Ruins', 'civilization' => 'Islamic', 'period' => 'Abbasid Period', 'region' => 'Cairo', 'location' => 'Old Cairo', 'description' => 'Archaeological remains of Fustat, the first capital of Islamic Egypt.'],
            ['seed_key' => 'al-azhar-mosque', 'name' => 'Al-Azhar Mosque', 'civilization' => 'Islamic', 'period' => 'Fatimid Dynasty', 'region' => 'Cairo', 'location' => 'Islamic Cairo', 'description' => 'A landmark mosque and center of learning established during the Fatimid era.'],
            ['seed_key' => 'citadel-of-cairo', 'name' => 'Citadel of Cairo', 'civilization' => 'Islamic', 'period' => 'Ayyubid Dynasty', 'region' => 'Cairo', 'location' => 'Cairo', 'description' => 'Historic Islamic fortress with panoramic city views.'],
            ['seed_key' => 'sultan-hassan-mosque', 'name' => 'Sultan Hassan Mosque', 'civilization' => 'Islamic', 'period' => 'Mamluk Sultanate', 'region' => 'Cairo', 'location' => 'Cairo', 'description' => 'A monumental Mamluk mosque known for its scale, symmetry, and architecture.'],
            ['seed_key' => 'muhammad-ali-mosque', 'name' => 'Muhammad Ali Mosque', 'civilization' => 'Islamic', 'period' => 'Ottoman Period', 'region' => 'Cairo', 'location' => 'Citadel of Cairo', 'description' => 'A defining Ottoman-era mosque that shaped the skyline of Cairo.'],

            ['seed_key' => 'hanging-church', 'name' => 'Hanging Church', 'civilization' => 'Coptic', 'period' => 'Early Christian Period', 'region' => 'Cairo', 'location' => 'Old Cairo', 'description' => 'One of the oldest Coptic churches in Old Cairo.'],
            ['seed_key' => 'saint-sergius-and-bacchus-church', 'name' => 'Saint Sergius and Bacchus Church', 'civilization' => 'Coptic', 'period' => 'Byzantine Period', 'region' => 'Cairo', 'location' => 'Old Cairo', 'description' => 'A revered church tied to the Holy Family tradition and Byzantine Christian heritage.'],
            ['seed_key' => 'babylon-fortress', 'name' => 'Babylon Fortress', 'civilization' => 'Coptic', 'period' => 'Islamic Transition', 'region' => 'Cairo', 'location' => 'Old Cairo', 'description' => 'The ancient fortress core that anchored Christian communities through the early Islamic era.'],
            ['seed_key' => 'monastery-of-saint-paul', 'name' => 'Monastery of Saint Paul', 'civilization' => 'Coptic', 'period' => 'Medieval Coptic Period', 'region' => 'Aswan', 'location' => 'Eastern Desert, Red Sea Governorate', 'description' => 'A historic monastery that reflects medieval Coptic monastic life and resilience.'],
            ['seed_key' => 'coptic-museum', 'name' => 'Coptic Museum', 'civilization' => 'Coptic', 'period' => 'Modern Coptic Era', 'region' => 'Cairo', 'location' => 'Old Cairo', 'description' => 'A major collection preserving the art and history of the Coptic community.'],

            ['seed_key' => 'pompeys-pillar', 'name' => 'Pompey\'s Pillar', 'civilization' => 'Greco-Roman', 'period' => 'Ptolemaic Dynasty', 'region' => 'Alexandria', 'location' => 'Alexandria', 'description' => 'A towering Roman-era column that represents the classical heritage of Alexandria.'],
            ['seed_key' => 'catacombs-of-kom-el-shoqafa', 'name' => 'Catacombs of Kom El Shoqafa', 'civilization' => 'Greco-Roman', 'period' => 'Roman Period', 'region' => 'Alexandria', 'location' => 'Alexandria', 'description' => 'A major Greco-Roman burial site in Alexandria.'],
            ['seed_key' => 'alexandria-amphitheatre', 'name' => 'Alexandria Amphitheatre', 'civilization' => 'Greco-Roman', 'period' => 'Byzantine Period', 'region' => 'Alexandria', 'location' => 'Alexandria', 'description' => 'A classical urban monument reflecting the layered Greco-Roman past of the city.'],

            ['seed_key' => 'suez-canal-museum', 'name' => 'Suez Canal Museum', 'civilization' => 'Modern Civilization', 'period' => 'Suez Canal & British Influence', 'region' => 'Cairo', 'location' => 'Ismailia', 'description' => 'A museum highlighting the canal era, imperial competition, and Egypt\'s strategic transformation.'],
            ['seed_key' => 'national-museum-of-egyptian-civilization', 'name' => 'National Museum of Egyptian Civilization', 'civilization' => 'Modern Civilization', 'period' => 'Egyptian Independence', 'region' => 'Cairo', 'location' => 'Fustat, Cairo', 'description' => 'A national institution symbolizing modern Egypt\'s cultural identity and post-revolution statehood.'],
            ['seed_key' => 'grand-egyptian-museum', 'name' => 'Grand Egyptian Museum', 'civilization' => 'Modern Civilization', 'period' => 'Contemporary Egypt', 'region' => 'Cairo', 'location' => 'Giza', 'description' => 'A flagship contemporary museum representing Egypt\'s modern tourism and heritage ambitions.'],
        ];

        foreach ($items as $item) {
            $civilization = Civilization::query()->where('name', $item['civilization'])->first();
            $region = Region::query()->where('name', $item['region'])->first();

            if (! $civilization || ! $region) {
                continue;
            }

            $period = CivilizationPeriod::query()
                ->where('title', $item['period'])
                ->where('civilization_id', $civilization->id)
                ->first();

            if (! $period) {
                continue;
            }

            Attraction::query()->firstOrCreate(
                ['seed_key' => $item['seed_key']],
                [
                    'name' => $item['name'],
                    'description' => $item['description'],
                    'image' => null,
                    'location' => $item['location'],
                    'civilization_id' => $civilization->id,
                    'civilization_period_id' => $period->id,
                    'region_id' => $region->id,
                ]
            );
        }
    }
}