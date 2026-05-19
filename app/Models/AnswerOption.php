<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnswerOption extends Model
{
    use HasFactory;

    protected $primaryKey = 'option_id';
    
    protected $fillable = ['question_id', 'option_text', 'score_value'];

    // Relasi: pilihan jawaban milik satu pertanyaan
    public function question()
    {
        return $this->belongsTo(MoodQuestion::class, 'question_id', 'question_id');
    }
}