<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add nullable FK column for selected option id
        Schema::table('exam_attempt_answers', function (Blueprint $table) {
            $table->foreignId('selected_option_id')->nullable()->after('selected_option')->constrained('question_options')->nullOnDelete();
        });

        // Populate selected_option_id for existing rows where possible
        // Join exam_attempt_answers -> question_options on question_id + option_key
        DB::statement(<<<'SQL'
            UPDATE `exam_attempt_answers` AS ea
            JOIN `question_options` AS qo
              ON qo.`question_id` = ea.`question_id` AND qo.`option_key` = ea.`selected_option`
            SET ea.`selected_option_id` = qo.`id`
            WHERE ea.`selected_option` IS NOT NULL;
        SQL
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_attempt_answers', function (Blueprint $table) {
            $table->dropForeign(['selected_option_id']);
            $table->dropColumn('selected_option_id');
        });
    }
};
