<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoinTransaction extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'subject_id', 'paper_id', 'attempt_id', 'coins', 'mode', 'earned_on', 'reason'];

    protected $casts = [
        'coins' => 'integer',
        'earned_on' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subject()
    {
        return $this->belongsTo(PastPaperSubject::class, 'subject_id');
    }

    public function paper()
    {
        return $this->belongsTo(PastPaper::class, 'paper_id');
    }

    public function attempt()
    {
        return $this->belongsTo(PastPaperAttempt::class, 'attempt_id');
    }
}
