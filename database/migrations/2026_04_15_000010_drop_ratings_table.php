<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('ratings');
    }

    public function down(): void
    {
        // Ratings are deprecated in favor of reviews and are not recreated.
    }
};
