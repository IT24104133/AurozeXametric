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
        Schema::create('coin_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained('past_paper_subjects')->cascadeOnDelete();
            $table->foreignId('paper_id')->nullable()->constrained('past_papers')->cascadeOnDelete();
            $table->foreignId('attempt_id')->nullable()->constrained('past_paper_attempts')->cascadeOnDelete();
            $table->integer('coins');
            $table->string('mode')->default('normal'); // normal, ultra_easy, ultra_medium, ultra_hard
            $table->date('earned_on');
            $table->string('reason')->nullable(); // e.g., "first_attempt_bonus", "perfect_score"
            $table->timestamps();

            // Index for daily limit checks
            $table->index(['user_id', 'earned_on']);
            
            // Unique constraint: prevent duplicate awards on same paper on same day
            $table->unique(['user_id', 'paper_id', 'earned_on']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coin_transactions');
    }
};
