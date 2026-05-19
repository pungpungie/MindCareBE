<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyJournal extends Model
{
    protected $primaryKey = 'journal_id';

protected $fillable = [
    'user_id', 'title', 'content', 
    'mood_before', 'mood_after', 'journal_date'
];

public function user()
{
    return $this->belongsTo(User::class);
}
}
