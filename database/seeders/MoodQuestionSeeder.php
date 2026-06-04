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

        // Pertanyaan 3
        $q3 = MoodQuestion::create([
            'question_text' => 'Saya merasa sedih atau murung tanpa alasan jelas',
            'category' => 'depresi'
        ]);
        
        AnswerOption::insert([
            ['question_id' => $q3->question_id, 'option_text' => 'Sangat Tidak Sesuai', 'score_value' => 1],
            ['question_id' => $q3->question_id, 'option_text' => 'Tidak Sesuai', 'score_value' => 2],
            ['question_id' => $q3->question_id, 'option_text' => 'Sesuai', 'score_value' => 3],
            ['question_id' => $q3->question_id, 'option_text' => 'Sangat Sesuai', 'score_value' => 4],
        ]);

        // Pertanyaan 4
        $q4 = MoodQuestion::create([
            'question_text' => 'Saya kehilangan minat pada aktivitas yang biasa disukai',
            'category' => 'depresi'
        ]);
        
        AnswerOption::insert([
            ['question_id' => $q4->question_id, 'option_text' => 'Sangat Tidak Sesuai', 'score_value' => 1],
            ['question_id' => $q4->question_id, 'option_text' => 'Tidak Sesuai', 'score_value' => 2],
            ['question_id' => $q4->question_id, 'option_text' => 'Sesuai', 'score_value' => 3],
            ['question_id' => $q4->question_id, 'option_text' => 'Sangat Sesuai', 'score_value' => 4],
        ]);

        // Pertanyaan 5
        $q5 = MoodQuestion::create([
            'question_text' => 'Saya mudah marah atau tersinggung',
            'category' => 'stres'
        ]);
        
        AnswerOption::insert([
            ['question_id' => $q5->question_id, 'option_text' => 'Sangat Tidak Sesuai', 'score_value' => 1],
            ['question_id' => $q5->question_id, 'option_text' => 'Tidak Sesuai', 'score_value' => 2],
            ['question_id' => $q5->question_id, 'option_text' => 'Sesuai', 'score_value' => 3],
            ['question_id' => $q5->question_id, 'option_text' => 'Sangat Sesuai', 'score_value' => 4],
        ]);

        // Pertanyaan 6
        $q6 = MoodQuestion::create([
            'question_text' => 'Saya sulit berkonsentrasi dalam melakukan tugas',
            'category' => 'stres'
        ]);
        
        AnswerOption::insert([
            ['question_id' => $q6->question_id, 'option_text' => 'Sangat Tidak Sesuai', 'score_value' => 1],
            ['question_id' => $q6->question_id, 'option_text' => 'Tidak Sesuai', 'score_value' => 2],
            ['question_id' => $q6->question_id, 'option_text' => 'Sesuai', 'score_value' => 3],
            ['question_id' => $q6->question_id, 'option_text' => 'Sangat Sesuai', 'score_value' => 4],
        ]);

        // Pertanyaan 7
        $q7 = MoodQuestion::create([
            'question_text' => 'Saya merasa kehilangan harapan atau putus asa',
            'category' => 'depresi'
        ]);
        
        AnswerOption::insert([
            ['question_id' => $q7->question_id, 'option_text' => 'Sangat Tidak Sesuai', 'score_value' => 1],
            ['question_id' => $q7->question_id, 'option_text' => 'Tidak Sesuai', 'score_value' => 2],
            ['question_id' => $q7->question_id, 'option_text' => 'Sesuai', 'score_value' => 3],
            ['question_id' => $q7->question_id, 'option_text' => 'Sangat Sesuai', 'score_value' => 4],
        ]);

        // Pertanyaan 8
        $q8 = MoodQuestion::create([
            'question_text' => 'Saya mudah tersinggung atau marah tanpa alasan jelas',
            'category' => 'stres'
        ]);
        AnswerOption::insert([
            ['question_id' => $q8->question_id, 'option_text' => 'Sangat Tidak Sesuai', 'score_value' => 1],
            ['question_id' => $q8->question_id, 'option_text' => 'Tidak Sesuai', 'score_value' => 2],
            ['question_id' => $q8->question_id, 'option_text' => 'Sesuai', 'score_value' => 3],
            ['question_id' => $q8->question_id, 'option_text' => 'Sangat Sesuai', 'score_value' => 4],
        ]);

        // Pertanyaan 9
        $q9 = MoodQuestion::create([
            'question_text' => 'Saya menghindari interaksi sosial atau bertemu orang lain',
            'category' => 'kecemasan'
        ]);
        AnswerOption::insert([
            ['question_id' => $q9->question_id, 'option_text' => 'Sangat Tidak Sesuai', 'score_value' => 1],
            ['question_id' => $q9->question_id, 'option_text' => 'Tidak Sesuai', 'score_value' => 2],
            ['question_id' => $q9->question_id, 'option_text' => 'Sesuai', 'score_value' => 3],
            ['question_id' => $q9->question_id, 'option_text' => 'Sangat Sesuai', 'score_value' => 4],
        ]);

        // Pertanyaan 10
        $q10 = MoodQuestion::create([
            'question_text' => 'Saya merasa tidak berharga atau merasa bersalah terus-menerus',
            'category' => 'depresi'
        ]);
        AnswerOption::insert([
            ['question_id' => $q10->question_id, 'option_text' => 'Sangat Tidak Sesuai', 'score_value' => 1],
            ['question_id' => $q10->question_id, 'option_text' => 'Tidak Sesuai', 'score_value' => 2],
            ['question_id' => $q10->question_id, 'option_text' => 'Sesuai', 'score_value' => 3],
            ['question_id' => $q10->question_id, 'option_text' => 'Sangat Sesuai', 'score_value' => 4],
        ]);

        // Pertanyaan 11
        $q11 = MoodQuestion::create([
            'question_text' => 'Saya mengalami gangguan tidur (sulit tidur, terbangun terlalu pagi, atau tidur berlebihan)',
            'category' => 'stres'
        ]);
        AnswerOption::insert([
            ['question_id' => $q11->question_id, 'option_text' => 'Sangat Tidak Sesuai', 'score_value' => 1],
            ['question_id' => $q11->question_id, 'option_text' => 'Tidak Sesuai', 'score_value' => 2],
            ['question_id' => $q11->question_id, 'option_text' => 'Sesuai', 'score_value' => 3],
            ['question_id' => $q11->question_id, 'option_text' => 'Sangat Sesuai', 'score_value' => 4],
        ]);
    }
}