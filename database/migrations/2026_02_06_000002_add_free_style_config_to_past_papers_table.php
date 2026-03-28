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
            $table->integer('total_questions')->nullable()->after('status');
            $table->integer('count_s')->nullable()->after('total_questions');
            $table->integer('count_m')->nullable()->after('count_s');
            $table->integer('count_h')->nullable()->after('count_m');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('past_papers', function (Blueprint $table) {
            $table->dropColumn(['total_questions', 'count_s', 'count_m', 'count_h']);
        });
    }
};
