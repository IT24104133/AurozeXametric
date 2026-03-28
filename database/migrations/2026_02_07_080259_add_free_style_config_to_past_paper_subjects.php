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
        Schema::table('past_paper_subjects', function (Blueprint $table) {
            // Free Style Configuration (Defaults will be set in model or seeder)
            $table->unsignedInteger('fs_total_questions')->default(40);
            
            // Normal mode difficulty distribution
            $table->unsignedInteger('fs_count_e')->default(12);
            $table->unsignedInteger('fs_count_m')->default(18);
            $table->unsignedInteger('fs_count_h')->default(10);
            
            // Ultra Easy mode: 20/15/5
            $table->unsignedInteger('fs_ultra_easy_e')->default(20);
            $table->unsignedInteger('fs_ultra_easy_m')->default(15);
            $table->unsignedInteger('fs_ultra_easy_h')->default(5);
            
            // Ultra Medium mode: 12/18/10
            $table->unsignedInteger('fs_ultra_medium_e')->default(12);
            $table->unsignedInteger('fs_ultra_medium_m')->default(18);
            $table->unsignedInteger('fs_ultra_medium_h')->default(10);
            
            // Ultra Hard mode: 5/20/15
            $table->unsignedInteger('fs_ultra_hard_e')->default(5);
            $table->unsignedInteger('fs_ultra_hard_m')->default(20);
            $table->unsignedInteger('fs_ultra_hard_h')->default(15);
            
            // Timer
            $table->unsignedInteger('fs_timer_minutes')->default(60);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('past_paper_subjects', function (Blueprint $table) {
            $table->dropColumn([
                'fs_total_questions',
                'fs_count_e', 'fs_count_m', 'fs_count_h',
                'fs_ultra_easy_e', 'fs_ultra_easy_m', 'fs_ultra_easy_h',
                'fs_ultra_medium_e', 'fs_ultra_medium_m', 'fs_ultra_medium_h',
                'fs_ultra_hard_e', 'fs_ultra_hard_m', 'fs_ultra_hard_h',
                'fs_timer_minutes',
            ]);
        });
    }
};
