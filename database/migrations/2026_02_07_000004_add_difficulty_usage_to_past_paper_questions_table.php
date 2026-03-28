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
        Schema::table('past_paper_questions', function (Blueprint $table) {
            if (!Schema::hasColumn('past_paper_questions', 'difficulty')) {
                $table->string('difficulty', 1)->default('M')->after('question_text');
            }
            if (!Schema::hasColumn('past_paper_questions', 'times_used')) {
                $table->integer('times_used')->default(0)->after('difficulty');
            }
            if (!Schema::hasColumn('past_paper_questions', 'last_used_at')) {
                $table->dateTime('last_used_at')->nullable()->after('times_used');
            }
        });

        Schema::table('past_paper_questions', function (Blueprint $table) {
            $table->index('difficulty');
            $table->index(['past_paper_id', 'difficulty']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('past_paper_questions', function (Blueprint $table) {
            $table->dropIndex(['difficulty']);
            $table->dropIndex(['past_paper_id', 'difficulty']);

            if (Schema::hasColumn('past_paper_questions', 'last_used_at')) {
                $table->dropColumn('last_used_at');
            }
            if (Schema::hasColumn('past_paper_questions', 'times_used')) {
                $table->dropColumn('times_used');
            }
            if (Schema::hasColumn('past_paper_questions', 'difficulty')) {
                $table->dropColumn('difficulty');
            }
        });
    }
};
