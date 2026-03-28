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
        Schema::create('exams', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->text('description')->nullable();
            $table->longText('instructions')->nullable();

            $table->unsignedInteger('duration_minutes')->default(30);

            // admin can schedule publish time
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();

            // question order mode
            $table->enum('question_mode', ['ordered', 'shuffled'])->default('ordered');

            $table->enum('status', ['draft', 'published'])->default('draft');

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
