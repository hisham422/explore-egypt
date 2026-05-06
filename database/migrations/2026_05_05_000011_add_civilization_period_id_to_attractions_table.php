<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attractions', function (Blueprint $table): void {
            $table->foreignId('civilization_period_id')
                ->nullable()
                ->after('civilization_id')
                ->constrained('civilization_periods')
                ->nullOnDelete();

            $table->index(['civilization_id', 'civilization_period_id', 'region_id'], 'attractions_civ_period_region_index');
        });
    }

    public function down(): void
    {
        Schema::table('attractions', function (Blueprint $table): void {
            $table->dropIndex('attractions_civ_period_region_index');
            $table->dropConstrainedForeignId('civilization_period_id');
        });
    }
};