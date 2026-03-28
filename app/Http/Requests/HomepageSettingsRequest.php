<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HomepageSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'hero_title' => ['required', 'string', 'max:255'],
            'hero_subtitle' => ['nullable', 'string', 'max:1000'],
            'hero_button_text' => ['required', 'string', 'max:255'],
            'hero_button_link' => ['required', 'string', 'max:255'],
            'hero_image' => ['nullable', 'image', 'max:2048'],

            'show_platform_stats' => ['required', 'boolean'],
            'show_leaderboard' => ['required', 'boolean'],
            'show_features' => ['required', 'boolean'],
            'show_testimonials' => ['required', 'boolean'],
            'show_growth_stats_section' => ['required', 'boolean'],
            'show_growth_chart' => ['required', 'boolean'],
            'growth_section_title' => ['required', 'string', 'max:255'],
            'growth_section_subtitle' => ['nullable', 'string', 'max:1000'],
            'stats_section_title' => ['required', 'string', 'max:255'],
            'stats_section_subtitle' => ['nullable', 'string', 'max:1000'],
            'stats_cards' => ['nullable', 'array'],
            'stats_cards.*.key' => ['required', 'string', 'max:50'],
            'stats_cards.*.label' => ['required', 'string', 'max:100'],
            'stats_cards.*.description' => ['nullable', 'string', 'max:255'],
            'stats_cards.*.icon' => ['nullable', 'string', 'max:50'],
            'stats_cards.*.enabled' => ['required', 'boolean'],
            'stats_cards.*.order' => ['required', 'integer', 'min:1', 'max:20'],

            'growth_start_date' => ['nullable', 'date'],
            'growth_end_date' => ['nullable', 'date', 'after_or_equal:growth_start_date'],
        ];
    }
}