<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PastPaperSubject extends Model
{
    protected $table = 'past_paper_subjects';

    protected $fillable = [
        'name',
        'stream',
        // Free Style Configuration
        'fs_total_questions',
        'fs_count_e',
        'fs_count_m',
        'fs_count_h',
        'fs_ultra_easy_e',
        'fs_ultra_easy_m',
        'fs_ultra_easy_h',
        'fs_ultra_medium_e',
        'fs_ultra_medium_m',
        'fs_ultra_medium_h',
        'fs_ultra_hard_e',
        'fs_ultra_hard_m',
        'fs_ultra_hard_h',
        'fs_timer_minutes',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get all past papers for this subject.
     */
    public function pastPapers(): HasMany
    {
        return $this->hasMany(PastPaper::class, 'subject_id');
    }
}
