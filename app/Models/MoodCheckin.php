<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MoodCheckin extends Model
{
    use HasFactory;

    protected $primaryKey = 'checkin_id';
    
    protected $fillable = [
        'user_id',
        'checkin_date',
        'overall_mood',
        'mood_score'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}