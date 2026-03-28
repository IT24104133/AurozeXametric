<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('past_paper_questions', function (Blueprint $table) {
            if (!Schema::hasColumn('past_paper_questions', 'question_image_1')) {
                $table->string('question_image_1')->nullable()->after('question_image');
            }
            if (!Schema::hasColumn('past_paper_questions', 'question_image_2')) {
                $table->string('question_image_2')->nullable()->after('question_image_1');
            }
            if (!Schema::hasColumn('past_paper_questions', 'question_image_3')) {
                $table->string('question_image_3')->nullable()->after('question_image_2');
            }
        });
    }

    public function down(): void
    {
        Schema::table('past_paper_questions', function (Blueprint $table) {
            if (Schema::hasColumn('past_paper_questions', 'question_image_3')) {
                $table->dropColumn('question_image_3');
            }
            if (Schema::hasColumn('past_paper_questions', 'question_image_2')) {
                $table->dropColumn('question_image_2');
            }
            if (Schema::hasColumn('past_paper_questions', 'question_image_1')) {
                $table->dropColumn('question_image_1');
            }
        });
    }
};
