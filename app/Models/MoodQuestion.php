<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MoodQuestion extends Model
{
    use HasFactory;

    protected $primaryKey = 'question_id';
    
    protected $fillable = ['question_text', 'category'];

    // Relasi: 1 pertanyaan punya banyak pilihan jawaban
    public function answerOptions()
    {
        return $this->hasMany(AnswerOption::class, 'question_id', 'question_id');
    }
}