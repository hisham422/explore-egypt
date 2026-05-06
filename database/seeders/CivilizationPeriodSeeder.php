<?php

namespace Database\Seeders;

use App\Models\Civilization;
use App\Models\CivilizationPeriod;
use Illuminate\Database\Seeder;

class CivilizationPeriodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $periods = [
            'Ancient Egypt' => [
                [
                    'title' => 'Early Dynastic Period',
                    'start_year' => -3100,
                    'end_year' => -2686,
                    'description' => 'The founding era of unified Egypt, characterized by the establishment of the first dynasties and the development of hierarchical governance.',
                    'rulers' => 'Narmer, Djoser',
                    'sort_order' => 1,
                ],
                [
                    'title' => 'Old Kingdom',
                    'start_year' => -2686,
                    'end_year' => -2181,
                    'description' => 'The "Age of the Pyramids" - Egypt\'s golden age of pyramid construction. Pharaohs were believed to be living gods, and massive architectural achievements defined this era.',
                    'rulers' => 'Khufu, Khafre, Menkaure, Pepi II',
                    'sort_order' => 2,
                ],
                [
                    'title' => 'Middle Kingdom',
                    'start_year' => -2055,
                    'end_year' => -1650,
                    'description' => 'A period of stability, cultural flourishing, and artistic achievement. Known for literature, art, and the development of a strong central government.',
                    'rulers' => 'Mentuhotep II, Amenemhat III',
                    'sort_order' => 3,
                ],
                [
                    'title' => 'New Kingdom',
                    'start_year' => -1550,
                    'end_year' => -1077,
                    'description' => 'Egypt\'s empire at its height. The era of great pharaohs like Ramesses and Tutankhamun, marked by military conquest, grand temples, and monumental achievement.',
                    'rulers' => 'Ahmose, Thutmose III, Hatshepsut, Amenhotep III, Tutankhamun, Ramesses II, Ramesses III',
                    'sort_order' => 4,
                ],
                [
                    'title' => 'Late Period',
                    'start_year' => -664,
                    'end_year' => -332,
                    'description' => 'A time of declining power and foreign influence. Egypt faced invasions from Persia and ultimately fell to the Greek conquest of Alexander the Great.',
                    'rulers' => 'Psamtik I, Necho II, Darius I',
                    'sort_order' => 5,
                ],
            ],
            'Islamic' => [
                [
                    'title' => 'Umayyad Period',
                    'start_year' => 661,
                    'end_year' => 750,
                    'description' => 'The earliest Islamic dynasty that expanded the caliphate across North Africa and the Middle East. Egypt became part of the Islamic world under Umayyad rule.',
                    'rulers' => 'Mu\'awiyah I, Umayyad Caliphs',
                    'sort_order' => 1,
                ],
                [
                    'title' => 'Abbasid Period',
                    'start_year' => 750,
                    'end_year' => 969,
                    'description' => 'A period of great intellectual and cultural achievement in the Islamic world. Baghdad became a center of learning while Egypt developed its own Islamic culture.',
                    'rulers' => 'Al-Mansur, Harun al-Rashid, Abbasid Caliphs',
                    'sort_order' => 2,
                ],
                [
                    'title' => 'Fatimid Dynasty',
                    'start_year' => 969,
                    'end_year' => 1171,
                    'description' => 'A powerful dynasty that ruled Egypt and established Cairo as their capital. Known for cultural refinement, architecture, and the establishment of Al-Azhar Mosque and University.',
                    'rulers' => 'Al-Mu\'izz li-Din Allah, Al-Hakim bi-Amr Allah, Al-Mustansir',
                    'sort_order' => 3,
                ],
                [
                    'title' => 'Ayyubid Dynasty',
                    'start_year' => 1171,
                    'end_year' => 1250,
                    'description' => 'Founded by Saladin, this dynasty is famous for military prowess and the construction of the Citadel of Cairo. A bridge between the Fatimid and Mamluk periods.',
                    'rulers' => 'Saladin (Salah ad-Din), Al-Aziz Uthman',
                    'sort_order' => 4,
                ],
                [
                    'title' => 'Mamluk Sultanate',
                    'start_year' => 1250,
                    'end_year' => 1517,
                    'description' => 'A militaristic society of slave-soldiers who became rulers. Known for their architecture, craftsmanship, and cultural contributions. Cairo flourished as a major Islamic center.',
                    'rulers' => 'Baybars, Qaitbay, Qansuh al-Ghuri',
                    'sort_order' => 5,
                ],
                [
                    'title' => 'Ottoman Period',
                    'start_year' => 1517,
                    'end_year' => 1882,
                    'description' => 'Egypt under Ottoman rule. A long period of relative stability, though with declining influence. Ottoman administrative systems governed Egypt until the arrival of European powers.',
                    'rulers' => 'Ottoman Sultans, Muhammad Ali Pasha, Khedives',
                    'sort_order' => 6,
                ],
            ],
            'Coptic' => [
                [
                    'title' => 'Early Christian Period',
                    'start_year' => 33,
                    'end_year' => 313,
                    'description' => 'The early Christian era in Egypt, marked by missionary activities and the growth of the Christian faith among the Egyptian population.',
                    'rulers' => 'Roman Emperors, Byzantine Emperors',
                    'sort_order' => 1,
                ],
                [
                    'title' => 'Byzantine Period',
                    'start_year' => 313,
                    'end_year' => 641,
                    'description' => 'Egypt as part of the Byzantine Empire. A flourishing Christian period with the construction of monasteries and churches. The Coptic Church became firmly established.',
                    'rulers' => 'Constantine, Justinian, Byzantine Emperors',
                    'sort_order' => 2,
                ],
                [
                    'title' => 'Islamic Transition',
                    'start_year' => 641,
                    'end_year' => 900,
                    'description' => 'The period following the Islamic conquest. Copts gradually adopted Islam, though significant Christian communities persisted. Formation of the modern Coptic Church.',
                    'rulers' => 'Arab Governors, Umayyad and Abbasid Caliphs',
                    'sort_order' => 3,
                ],
                [
                    'title' => 'Medieval Coptic Period',
                    'start_year' => 900,
                    'end_year' => 1517,
                    'description' => 'A time when Copts formed a minority Christian community within Islamic Egypt. Despite challenges, they maintained their faith, culture, and unique ecclesiastical traditions.',
                    'rulers' => 'Fatimid, Ayyubid, and Mamluk Rulers',
                    'sort_order' => 4,
                ],
                [
                    'title' => 'Modern Coptic Era',
                    'start_year' => 1517,
                    'end_year' => 2100,
                    'description' => 'From Ottoman times to the present. Copts have played important roles in Egyptian society, contributing to education, commerce, and culture while maintaining their Christian heritage.',
                    'rulers' => 'Ottoman Rulers, Modern Egypt',
                    'sort_order' => 5,
                ],
            ],
            'Greco-Roman' => [
                [
                    'title' => 'Ptolemaic Dynasty',
                    'start_year' => -332,
                    'end_year' => -30,
                    'description' => 'Founded by Ptolemy I after Alexander the Great\'s conquest. A Greek dynasty that ruled Egypt for 300 years, blending Greek and Egyptian cultures.',
                    'rulers' => 'Ptolemy I, Ptolemy II, Cleopatra VII',
                    'sort_order' => 1,
                ],
                [
                    'title' => 'Roman Period',
                    'start_year' => 30,
                    'end_year' => 395,
                    'description' => 'Egypt became a Roman province after the defeat of Cleopatra. A period of peace, prosperity, and cultural synthesis between Roman and Egyptian traditions.',
                    'rulers' => 'Octavian (Augustus), Julius Caesar, Roman Emperors',
                    'sort_order' => 2,
                ],
                [
                    'title' => 'Byzantine Period',
                    'start_year' => 395,
                    'end_year' => 641,
                    'description' => 'Egypt as part of the Eastern Roman (Byzantine) Empire. Continued prosperity with the flourishing of Christianity and the development of a unique Greco-Roman-Christian culture.',
                    'rulers' => 'Byzantine Emperors, Justinian I',
                    'sort_order' => 3,
                ],
            ],
            'Modern Civilization' => [
                [
                    'title' => 'Muhammad Ali Era',
                    'start_year' => 1805,
                    'end_year' => 1848,
                    'description' => 'Muhammad Ali Pasha modernized Egypt, establishing a modern army, navy, and administrative system. He laid the foundation for modern Egypt\'s infrastructure and development.',
                    'rulers' => 'Muhammad Ali Pasha',
                    'sort_order' => 1,
                ],
                [
                    'title' => 'Suez Canal & British Influence',
                    'start_year' => 1869,
                    'end_year' => 1952,
                    'description' => 'The construction of the Suez Canal transformed Egypt into a crucial crossroads. British occupation and influence shaped the nation\'s politics and society during this period.',
                    'rulers' => 'Ismail Pasha, Khedives, British Colonial Administration',
                    'sort_order' => 2,
                ],
                [
                    'title' => 'Egyptian Independence',
                    'start_year' => 1952,
                    'end_year' => 2000,
                    'description' => 'Following the revolution, Egypt gained independence. The Nasser and Sadat eras brought modernization, the Suez Crisis, peace with Israel, and significant social transformation.',
                    'rulers' => 'Gamal Abdel Nasser, Anwar Sadat, Hosni Mubarak',
                    'sort_order' => 3,
                ],
                [
                    'title' => 'Contemporary Egypt',
                    'start_year' => 2000,
                    'end_year' => 2100,
                    'description' => 'Modern Egypt navigating 21st-century challenges and opportunities. Continued cultural significance, tourism expansion, technological advancement, and regional influence.',
                    'rulers' => 'Hosni Mubarak, Mohamed Morsi, Abdel Fattah el-Sisi',
                    'sort_order' => 4,
                ],
            ],
        ];

        foreach ($periods as $civilizationName => $periodsList) {
            $civilization = Civilization::where('name', $civilizationName)->first();

            if (!$civilization) {
                continue;
            }

            foreach ($periodsList as $periodData) {
                CivilizationPeriod::updateOrCreate(
                    [
                        'civilization_id' => $civilization->id,
                        'title' => $periodData['title'],
                    ],
                    $periodData
                );
            }
        }
    }
}
