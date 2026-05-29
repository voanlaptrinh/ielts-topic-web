<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('practice_tests', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('skill', 20);
            $table->string('level', 50)->default('intermediate');
            $table->unsignedInteger('duration_minutes')->default(20);
            $table->text('description')->nullable();
            $table->longText('passage')->nullable();
            $table->longText('transcript')->nullable();
            $table->string('audio_path')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();

            $table->index(['skill', 'is_published']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('practice_tests');
    }
};
