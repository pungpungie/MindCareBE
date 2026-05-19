<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MoodQuestion;
use App\Models\AnswerOption;
use App\Models\MoodCheckin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MoodCheckController extends Controller
{
    /**
     * GET: Ambil semua pertanyaan dengan pilihan jawaban
     */
    public function getQuestions()
    {
        $questions = MoodQuestion::with('answerOptions')->get();

        return response()->json([
            'status' => true,
            'message' => 'Data pertanyaan berhasil diambil',
            'data' => $questions
        ], 200);
    }

    /**
     * POST: Submit jawaban mood check
     */
    public function submitMoodCheck(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'answers' => 'required|array|min:1',
            'answers.*.question_id' => 'required|exists:mood_questions,question_id',
            'answers.*.option_id' => 'required|exists:answer_options,option_id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Hitung total skor
        $totalScore = 0;
        $detailJawaban = [];

        foreach ($request->answers as $answer) {
            $option = AnswerOption::with('question')->find($answer['option_id']);
            
            if ($option) {
                $totalScore += $option->score_value;
                
                // Simpan detail jawaban untuk response
                $detailJawaban[] = [
                    'pertanyaan' => $option->question->question_text,
                    'jawaban' => $option->option_text,
                    'skor' => $option->score_value
                ];
            }
        }

        // Tentukan diagnosis berdasarkan total skor
        $diagnosis = $this->getDiagnosis($totalScore);

        // Simpan ke database
        $checkin = MoodCheckin::create([
            'user_id' => $request->user()->id,
            'checkin_date' => now()->toDateString(),
            'overall_mood' => $diagnosis,
            'mood_score' => $totalScore
        ]);

        // Kembalikan response lengkap
        return response()->json([
            'status' => true,
            'message' => 'Mood check berhasil disimpan',
            'data' => [
                'checkin_id' => $checkin->checkin_id,
                'tanggal' => $checkin->checkin_date,
                'total_skor' => $totalScore,
                'diagnosis' => $diagnosis,
                'detail_jawaban' => $detailJawaban
            ]
        ], 201);
    }

    /**
     * GET: Lihat riwayat mood check user
     */
    public function getHistory(Request $request)
    {
        $history = MoodCheckin::where('user_id', $request->user()->id)
                    ->orderBy('checkin_date', 'desc')
                    ->get();

        return response()->json([
            'status' => true,
            'message' => 'Riwayat mood check',
            'data' => $history
        ], 200);
    }

    /**
     * Fungsi untuk menentukan diagnosis berdasarkan skor
     */
    private function getDiagnosis($score)
    {
        if ($score <= 15) {
            return 'Mood Baik';
        } elseif ($score <= 25) {
            return 'Stres Ringan';
        } elseif ($score <= 35) {
            return 'Stres Sedang';
        } else {
            return 'Stres Berat';
        }
    }
}