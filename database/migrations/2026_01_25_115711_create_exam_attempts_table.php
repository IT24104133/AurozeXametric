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
        Schema::create('exam_attempts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('exam_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->enum('status', ['in_progress', 'submitted', 'auto_submitted'])->default('in_progress');

            $table->timestamp('started_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('submitted_at')->nullable();

            $table->unsignedInteger('score')->nullable(); // 0-40
            $table->unsignedInteger('total_questions')->default(40);

            // store question order per student attempt
            $table->json('question_order')->nullable();

            $table->timestamps();

            $table->unique(['exam_id', 'user_id']); // one attempt per exam (change later if you want retry)
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_attempts');
    }
};
