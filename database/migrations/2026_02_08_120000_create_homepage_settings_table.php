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
        Schema::create('homepage_settings', function (Blueprint $table) {
            $table->id();
            $table->string('hero_title');
            $table->text('hero_subtitle')->nullable();
            $table->string('hero_button_text')->default('Start Practicing');
            $table->string('hero_button_link')->default('/login');
            $table->string('hero_image_path')->nullable();

            $table->boolean('show_platform_stats')->default(true);
            $table->boolean('show_leaderboard')->default(true);
            $table->boolean('show_features')->default(true);
            $table->boolean('show_testimonials')->default(false);
            $table->boolean('show_growth_widget')->default(true);

            $table->date('growth_start_date')->nullable();
            $table->date('growth_end_date')->nullable();

            $table->boolean('is_published')->default(true);
            $table->json('draft_json')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('homepage_settings');
    }
};