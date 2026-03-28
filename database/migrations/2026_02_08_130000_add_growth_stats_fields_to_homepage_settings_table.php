<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('homepage_settings', function (Blueprint $table) {
            $table->boolean('show_growth_stats_section')->default(true)->after('show_growth_widget');
            $table->boolean('show_growth_chart')->default(true)->after('show_growth_stats_section');
            $table->string('growth_section_title')->default('Growth & Statistics')->after('show_growth_chart');
            $table->text('growth_section_subtitle')->nullable()->after('growth_section_title');
            $table->string('stats_section_title')->default('By The Numbers')->after('growth_section_subtitle');
            $table->text('stats_section_subtitle')->nullable()->after('stats_section_title');
            $table->json('stats_cards')->nullable()->after('stats_section_subtitle');
        });
    }

    public function down(): void
    {
        Schema::table('homepage_settings', function (Blueprint $table) {
            $table->dropColumn([
                'show_growth_stats_section',
                'show_growth_chart',
                'growth_section_title',
                'growth_section_subtitle',
                'stats_section_title',
                'stats_section_subtitle',
                'stats_cards',
            ]);
        });
    }
};