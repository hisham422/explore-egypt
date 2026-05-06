<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('civilizations', function (Blueprint $table) {
            $table->string('hero_video_url')->nullable()->after('image');
        });
    }

    public function down(): void
    {
        Schema::table('civilizations', function (Blueprint $table) {
            $table->dropColumn('hero_video_url');
        });
    }
};