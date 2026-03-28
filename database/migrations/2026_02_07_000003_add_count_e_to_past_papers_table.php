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
        Schema::table('past_papers', function (Blueprint $table) {
            if (!Schema::hasColumn('past_papers', 'count_e')) {
                $table->integer('count_e')->default(12)->after('total_questions');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('past_papers', function (Blueprint $table) {
            if (Schema::hasColumn('past_papers', 'count_e')) {
                $table->dropColumn('count_e');
            }
        });
    }
};
