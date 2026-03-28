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
        Schema::table('exams', function (Blueprint $table) {
            // Students must answer only N questions (ex: 40)
            if (!Schema::hasColumn('exams', 'question_limit')) {
                $table->unsignedInteger('question_limit')->default(40)->after('duration_minutes');
            }

            // How to select questions from pool: all | first_n | random_n | manual
            if (!Schema::hasColumn('exams', 'selection_mode')) {
                $table->string('selection_mode', 20)->default('all')->after('question_limit');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            if (Schema::hasColumn('exams', 'selection_mode')) {
                $table->dropColumn('selection_mode');
            }

            if (Schema::hasColumn('exams', 'question_limit')) {
                $table->dropColumn('question_limit');
            }
        });
    }
};
