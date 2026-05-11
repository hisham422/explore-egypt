<?php

use Illuminate\Support\Facades\DB;

return new class {
    public function run()
    {
        $csvPath = storage_path('exports/attractions_descriptions.csv');

        if (!file_exists($csvPath)) {
            echo "CSV file not found at: $csvPath\n";
            return;
        }

        $handle = fopen($csvPath, 'r');
        $header = fgetcsv($handle); // Skip header row

        $updated = 0;
        $errors = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 3) {
                continue; // Skip malformed rows
            }

            $id = (int) $row[0];
            $name = $row[1] ?? '';
            $description = $row[2] ?? '';

            if (empty($id) || empty($description)) {
                continue;
            }

            try {
                DB::table('attractions')
                    ->where('id', $id)
                    ->update([
                        'description' => $description,
                        'updated_at' => now(),
                    ]);

                $updated++;
            } catch (\Exception $e) {
                $errors++;
                echo "Error updating attraction {$id}: {$e->getMessage()}\n";
            }
        }

        fclose($handle);

        echo "✓ Imported $updated descriptions from CSV\n";
        if ($errors > 0) {
            echo "✗ Encountered $errors errors during import\n";
        }
    }
};
