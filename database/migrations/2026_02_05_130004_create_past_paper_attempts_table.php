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
        Schema::create('past_paper_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('past_paper_id')->constrained('past_papers')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->integer('attempt_no')->default(1);
            $table->enum('status', ['in_progress', 'submitted'])->default('submitted');
            $table->integer('score')->default(0);
            $table->integer('total_questions')->default(0);
            $table->integer('correct_count')->default(0);
            $table->decimal('percentage', 5, 2)->default(0);
            $table->timestamps();

            // Indexes
            $table->index(['student_id', 'past_paper_id']);
            $table->index(['past_paper_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('past_paper_attempts');
    }
};
