<?php

namespace App\Http\Controllers\Api;

use App\Models\MoodQuestion;
use App\Models\AnswerOption;
use App\Models\MoodCheckin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MoodCheckController extends BaseController
{
    /**
     * Ambil semua pertanyaan dengan pilihan jawaban
     */
    public function getQuestions()
    {
        $questions = MoodQuestion::with('answerOptions')->get();

        if ($questions->isEmpty()) {
            return $this->errorResponse('Belum ada pertanyaan tersedia', 404);
        }

        return $this->successResponse($questions, 'Data pertanyaan berhasil diambil');
    }

    /**
     * Submit jawaban mood check
     */
    public function submitMoodCheck(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'answers' => 'required|array|min:1',
            'answers.*.question_id' => 'required|exists:mood_questions,question_id',
            'answers.*.option_id' => 'required|exists:answer_options,option_id'
        ], [
            'answers.min' => 'Minimal 1 jawaban harus diisi'
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        $totalScore = 0;
        $detailJawaban = [];

        foreach ($request->answers as $answer) {
            $option = AnswerOption::with('question')->find($answer['option_id']);
            
            if ($option) {
                $totalScore += $option->score_value;
                
                $detailJawaban[] = [
                    'pertanyaan' => $option->question->question_text,
                    'kategori' => $option->question->category,
                    'jawaban' => $option->option_text,
                    'skor' => $option->score_value
                ];
            }
        }

        $diagnosis = $this->getDiagnosis($totalScore);
        $saran = $this->getSaran($diagnosis);

        $checkin = MoodCheckin::create([
            'user_id' => $request->user()->id,
            'checkin_date' => now()->toDateString(),
            'overall_mood' => $diagnosis,
            'mood_score' => $totalScore
        ]);

        return $this->successResponse([
            'checkin_id' => $checkin->checkin_id,
            'tanggal' => $checkin->checkin_date,
            'total_skor' => $totalScore,
            'diagnosis' => $diagnosis,
            'saran' => $saran,
            'detail_jawaban' => $detailJawaban
        ], 'Mood check berhasil disimpan', 201);
    }

    /**
     * Riwayat mood check user
     */
    public function getHistory(Request $request)
    {
        $history = MoodCheckin::where('user_id', $request->user()->id)
                    ->orderBy('checkin_date', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->get();

        $trendMessage = $this->calculateTrend($history);

        return $this->successResponse([
            'total_check' => $history->count(),
            'trend' => $trendMessage,
            'history' => $history
        ], 'Riwayat mood check');
    }

    /**
     * Fungsi diagnosis dengan rentang yang lebih masuk akal
     * Asumsi: skor 1-4 per pertanyaan, total pertanyaan 6-10.
     */
    private function getDiagnosis($score)
{
    if ($score <= 18) {
        return 'Mood Baik';
    } elseif ($score <= 26) {
        return 'Stres Ringan';
    } elseif ($score <= 34) {
        return 'Stres Sedang';
    } else {
        return 'Stres Berat';
    }
}

    /**
     * Saran berdasarkan diagnosis
     */
    private function getSaran($diagnosis)
    {
        $saran = [
            'Mood Baik' => 'Pertahankan mood positifmu! Tetap lakukan aktivitas yang menyenangkan.',
            'Stres Ringan' => 'Coba relaksasi ringan seperti mendengarkan musik atau jalan santai.',
            'Stres Sedang' => 'Pertimbangkan untuk meditasi, olahraga teratur, atau bicara dengan teman.',
            'Stres Berat' => 'Sangat disarankan untuk berkonsultasi dengan profesional kesehatan mental.'
        ];

        return $saran[$diagnosis] ?? 'Jaga selalu kesehatan mentalmu.';
    }

    /**
     * Hitung tren mood berdasarkan 5 data terakhir
     */
    private function calculateTrend($history)
    {
        if ($history->count() < 2) {
            return 'Data belum cukup';
        }

        // Ambil maksimal 5 data terbaru, lalu balik agar urut dari lama ke baru
        $recentHistory = $history->take(5)->reverse()->values();
        $scores = $recentHistory->pluck('mood_score')->toArray();
        $count = count($scores);

        // Bagi dua: set pertama vs set kedua
        $mid = intdiv($count, 2);
        $firstHalf = array_slice($scores, 0, $mid);
        $secondHalf = array_slice($scores, $mid);

        $avgFirst = array_sum($firstHalf) / count($firstHalf);
        $avgSecond = array_sum($secondHalf) / count($secondHalf);

        if ($avgSecond < $avgFirst) {
            return 'Membaik 📉';
        } elseif ($avgSecond > $avgFirst) {
            return 'Perlu perhatian 📈';
        } else {
            return 'Stabil ➡️';
        }
    }
}