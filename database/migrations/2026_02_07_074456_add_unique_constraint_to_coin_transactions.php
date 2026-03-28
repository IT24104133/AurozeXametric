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
        Schema::table('coin_transactions', function (Blueprint $table) {
            // Unique constraint: prevent duplicate awards on same paper on same day
            $table->unique(['user_id', 'paper_id', 'earned_on']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coin_transactions', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'paper_id', 'earned_on']);
        });
    }
};
