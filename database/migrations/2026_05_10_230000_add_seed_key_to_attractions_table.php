<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attractions', function (Blueprint $table): void {
            $table->string('seed_key')->nullable()->unique()->after('name');
        });

        DB::table('attractions')
            ->select(['id', 'name'])
            ->orderBy('id')
            ->chunkById(100, function ($attractions): void {
                foreach ($attractions as $attraction) {
                    DB::table('attractions')
                        ->where('id', $attraction->id)
                        ->update([
                            'seed_key' => Str::slug($attraction->name),
                        ]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('attractions', function (Blueprint $table): void {
            $table->dropUnique(['seed_key']);
            $table->dropColumn('seed_key');
        });
    }
};