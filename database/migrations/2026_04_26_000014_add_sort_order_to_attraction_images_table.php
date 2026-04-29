<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attraction_images', function (Blueprint $table): void {
            $table->unsignedInteger('sort_order')->default(0)->after('image');
        });
    }

    public function down(): void
    {
        Schema::table('attraction_images', function (Blueprint $table): void {
            $table->dropColumn('sort_order');
        });
    }
};
