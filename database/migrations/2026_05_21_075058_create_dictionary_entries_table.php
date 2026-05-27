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
        Schema::create('dictionary_entries', function (Blueprint $table) {
            $table->id();
            $table->string('word');
            $table->string('normalized_word');
            $table->string('part_of_speech', 20);
            $table->text('definition');
            $table->text('definition_vi')->nullable();
            $table->json('examples')->nullable();
            $table->json('synonyms')->nullable();
            $table->string('source', 60)->default('Open English WordNet 2025');
            $table->string('source_id')->unique();
            $table->timestamps();

            $table->index('normalized_word');
            $table->index('part_of_speech');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dictionary_entries');
    }
};
