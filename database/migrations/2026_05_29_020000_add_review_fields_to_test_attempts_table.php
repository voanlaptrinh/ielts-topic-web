<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('test_attempts', function (Blueprint $table) {
            $table->decimal('band_score', 3, 1)->nullable()->after('total');
            $table->text('feedback')->nullable()->after('band_score');
            $table->json('criteria_scores')->nullable()->after('feedback');
            $table->foreignId('reviewed_by')->nullable()->after('criteria_scores')->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
            $table->index(['test_type', 'reviewed_at']);
        });
    }

    public function down(): void
    {
        Schema::table('test_attempts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('reviewed_by');
            $table->dropIndex(['test_type', 'reviewed_at']);
            $table->dropColumn(['band_score', 'feedback', 'criteria_scores', 'reviewed_at']);
        });
    }
};
