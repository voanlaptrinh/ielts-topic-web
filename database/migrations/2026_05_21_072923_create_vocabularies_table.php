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
        Schema::create('vocabularies', function (Blueprint $table) {
            $table->id();
            $table->string('word')->unique();
            $table->string('phonetic')->nullable();
            $table->string('part_of_speech');
            $table->text('meaning_vi');
            $table->text('definition_en');
            $table->text('example_en');
            $table->text('example_vi');
            $table->string('topic')->nullable();
            $table->string('level', 20)->default('B1');
            $table->json('synonyms')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vocabularies');
    }
};
