<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE attractions DROP FOREIGN KEY attractions_civilization_id_foreign');
        DB::statement('ALTER TABLE attractions MODIFY civilization_id BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE attractions ADD CONSTRAINT attractions_civilization_id_foreign FOREIGN KEY (civilization_id) REFERENCES civilizations(id) ON DELETE SET NULL');
    }

    public function down(): void
    {
        $fallbackCivilizationId = DB::table('civilizations')->min('id');

        if ($fallbackCivilizationId !== null) {
            DB::table('attractions')
                ->whereNull('civilization_id')
                ->update(['civilization_id' => $fallbackCivilizationId]);
        }

        DB::statement('ALTER TABLE attractions DROP FOREIGN KEY attractions_civilization_id_foreign');
        DB::statement('ALTER TABLE attractions MODIFY civilization_id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE attractions ADD CONSTRAINT attractions_civilization_id_foreign FOREIGN KEY (civilization_id) REFERENCES civilizations(id) ON DELETE CASCADE');
    }
};
