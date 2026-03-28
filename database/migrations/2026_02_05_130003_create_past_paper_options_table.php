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
        Schema::create('past_paper_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('past_paper_questions')->cascadeOnDelete();
            $table->string('option_key', 1); // A, B, C, D
            $table->text('option_text')->nullable();
            $table->string('option_image')->nullable();
            $table->boolean('is_correct')->default(false);
            $table->integer('position')->default(1);
            $table->timestamps();

            // Unique constraint
            $table->unique(['question_id', 'option_key']);

            // Index
            $table->index(['question_id', 'position']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('past_paper_options');
    }
};
