<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('attraction_images')
            ->whereNull('type')
            ->orWhere('type', '')
            ->update(['type' => 'image']);

        $all = DB::table('attraction_images')->select('id', 'image')->get();

        foreach ($all as $row) {
            $path = strtolower((string) ($row->image ?? ''));
            $type = str_ends_with($path, '.mp4') ? 'video' : 'image';

            DB::table('attraction_images')
                ->where('id', $row->id)
                ->update(['type' => $type]);
        }
    }

    public function down(): void
    {
        // Keep existing detected values on rollback.
    }
};
