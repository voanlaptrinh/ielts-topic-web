<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('target_band', 10)->nullable()->after('is_admin');
            $table->string('study_minutes_per_day', 10)->default('25')->after('target_band');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['target_band', 'study_minutes_per_day']);
        });
    }
};
