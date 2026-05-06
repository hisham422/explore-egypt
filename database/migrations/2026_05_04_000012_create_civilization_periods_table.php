<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('civilization_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('civilization_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->integer('start_year');
            $table->integer('end_year');
            $table->text('description');
            $table->text('rulers')->nullable();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamps();

            $table->index(['civilization_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('civilization_periods');
    }
};
