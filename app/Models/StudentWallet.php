<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentWallet extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'total_coins'];

    protected $casts = [
        'total_coins' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(CoinTransaction::class, 'user_id', 'user_id');
    }
}
