<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomepageSetting extends Model
{
    protected $fillable = [
        'hero_title',
        'hero_subtitle',
        'hero_button_text',
        'hero_button_link',
        'hero_image_path',
        'show_platform_stats',
        'show_leaderboard',
        'show_features',
        'show_testimonials',
        'show_growth_widget',
        'show_growth_stats_section',
        'show_growth_chart',
        'growth_section_title',
        'growth_section_subtitle',
        'stats_section_title',
        'stats_section_subtitle',
        'stats_cards',
        'growth_start_date',
        'growth_end_date',
        'is_published',
        'draft_json',
        'updated_by',
    ];

    protected $casts = [
        'show_platform_stats' => 'boolean',
        'show_leaderboard' => 'boolean',
        'show_features' => 'boolean',
        'show_testimonials' => 'boolean',
        'show_growth_widget' => 'boolean',
        'show_growth_stats_section' => 'boolean',
        'show_growth_chart' => 'boolean',
        'growth_start_date' => 'date',
        'growth_end_date' => 'date',
        'stats_cards' => 'array',
        'is_published' => 'boolean',
        'draft_json' => 'array',
    ];
}