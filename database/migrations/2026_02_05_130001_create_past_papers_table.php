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
        Schema::create('past_papers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained('past_paper_subjects')->cascadeOnDelete();
            $table->enum('category', ['edu_department', 'free_style']);
            $table->integer('year')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->integer('duration_minutes')->default(60);
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->timestamps();

            // Indexes
            $table->index(['category', 'subject_id']);
            $table->index(['subject_id', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('past_papers');
    }
};
