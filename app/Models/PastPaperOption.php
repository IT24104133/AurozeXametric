<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PastPaperOption extends Model
{
    protected $table = 'past_paper_options';

    protected $fillable = [
        'question_id',
        'option_key',
        'option_text',
        'option_image',
        'is_correct',
        'position',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'position' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the question this option belongs to.
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(PastPaperQuestion::class, 'question_id');
    }
}
