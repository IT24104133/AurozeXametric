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
        Schema::create('past_paper_attempt_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')->constrained('past_paper_attempts')->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('past_paper_questions')->cascadeOnDelete();
            $table->foreignId('selected_option_id')->nullable()->constrained('past_paper_options')->cascadeOnDelete();
            $table->boolean('is_correct')->default(false);
            $table->timestamps();

            // Unique constraint
            $table->unique(['attempt_id', 'question_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('past_paper_attempt_answers');
    }
};
