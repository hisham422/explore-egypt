<?php

namespace Database\Seeders;

use App\Models\Attraction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportDescriptionsSeeder extends Seeder
{
    public function run(): void
    {
        $csvPath = storage_path('exports/attractions_descriptions.csv');
        $seedKeyOverrides = [
            'Pompey\'s Pillar' => 'pompeys-pillar',
        ];

        if (!file_exists($csvPath)) {
            $this->command->error("CSV file not found at: $csvPath");
            return;
        }

        $handle = fopen($csvPath, 'r');
        $header = fgetcsv($handle);
        
        $updated = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 3) {
                continue;
            }

            // Skip if row doesn't have proper structure
            $name = trim($row[1] ?? '');
            $description = trim($row[2] ?? '');

            if (empty($name) || empty($description)) {
                continue;
            }

            $seedKey = $seedKeyOverrides[$name] ?? Str::slug($name);

            if ($seedKey === '' || ! Attraction::query()->where('seed_key', $seedKey)->exists()) {
                continue;
            }

            $updated_count = DB::table('attractions')
                ->where('seed_key', $seedKey)
                ->where(function ($query) {
                    $query->whereNull('description')
                        ->orWhere('description', '=', '');
                })
                ->update([
                    'description' => $description,
                    'updated_at' => now(),
                ]);

            if ($updated_count > 0) {
                $this->command->line("✓ Updated: $name");
                $updated++;
            }
        }

        fclose($handle);

        $this->command->info("✓ Successfully imported {$updated} descriptions from CSV");
    }
}
