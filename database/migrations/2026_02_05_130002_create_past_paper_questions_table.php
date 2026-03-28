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
        Schema::create('past_paper_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('past_paper_id')->constrained('past_papers')->cascadeOnDelete();
            $table->longText('question_text');
            $table->string('question_image')->nullable();
            $table->longText('explanation')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('past_paper_questions');
    }
};
