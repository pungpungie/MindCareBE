<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MoodQuestion;
use App\Models\AnswerOption;

class MoodQuestionSeeder extends Seeder
{
    public function run(): void
    {
        // Pertanyaan 1
        $q1 = MoodQuestion::create([
            'question_text' => 'Saya merasa tegang atau gelisah',
            'category' => 'kecemasan'
        ]);
        
        AnswerOption::insert([
            ['question_id' => $q1->question_id, 'option_text' => 'Sangat Tidak Sesuai', 'score_value' => 1],
            ['question_id' => $q1->question_id, 'option_text' => 'Tidak Sesuai', 'score_value' => 2],
            ['question_id' => $q1->question_id, 'option_text' => 'Sesuai', 'score_value' => 3],
            ['question_id' => $q1->question_id, 'option_text' => 'Sangat Sesuai', 'score_value' => 4],
        ]);

        // Pertanyaan 2
        $q2 = MoodQuestion::create([
            'question_text' => 'Saya sulit tidur atau sering terbangun di malam hari',
            'category' => 'kecemasan'
        ]);
        
        AnswerOption::insert([
            ['question_id' => $q2->question_id, 'option_text' => 'Sangat Tidak Sesuai', 'score_value' => 1],
            ['question_id' => $q2->question_id, 'option_text' => 'Tidak Sesuai', 'score_value' => 2],
            ['question_id' => $q2->question_id, 'option_text' => 'Sesuai', 'score_value' => 3],
            ['question_id' => $q2->question_id, 'option_text' => 'Sangat Sesuai', 'score_value' => 4],
        ]);

        // Tambah pertanyaan lain (minimal 10 pertanyaan)
        // ...
    }
}