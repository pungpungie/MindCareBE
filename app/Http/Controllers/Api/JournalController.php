<?php

namespace App\Http\Controllers\Api;

use App\Models\DailyJournal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JournalController extends BaseController
{
    /**
     * List jurnal user
     */
    public function index(Request $request)
    {
        $query = DailyJournal::where('user_id', $request->user()->id)
                    ->orderBy('journal_date', 'desc');

        // Filter by date
        if ($request->has('date')) {
            $query->whereDate('journal_date', $request->date);
        }

        // Filter by month
        if ($request->has('month')) {
            $query->whereMonth('journal_date', $request->month);
        }

        $journals = $query->get();

        // Hitung statistik
        $stats = [
            'total_jurnal' => $journals->count(),
            'rata_mood_before' => round($journals->avg('mood_before'), 1),
            'rata_mood_after' => round($journals->avg('mood_after'), 1),
        ];

        return $this->successResponse([
            'stats' => $stats,
            'journals' => $journals
        ], 'Daftar jurnal harian');
    }

    /**
     * Buat jurnal baru
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:10',
            'mood_before' => 'required|integer|min:1|max:10',
            'mood_after' => 'required|integer|min:1|max:10',
            'journal_date' => 'required|date|before_or_equal:today'
        ], [
            'content.min' => 'Isi jurnal minimal 10 karakter',
            'journal_date.before_or_equal' => 'Tanggal jurnal tidak boleh lebih dari hari ini'
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        $journal = DailyJournal::create([
            'user_id' => $request->user()->id,
            'title' => $request->title,
            'content' => $request->content,
            'mood_before' => $request->mood_before,
            'mood_after' => $request->mood_after,
            'journal_date' => $request->journal_date
        ]);

        // Analisis mood
        $moodChange = $request->mood_after - $request->mood_before;
        $moodAnalysis = $this->analyzeMoodChange($moodChange);

        return $this->successResponse([
            'journal' => $journal,
            'mood_change' => $moodChange,
            'mood_analysis' => $moodAnalysis
        ], 'Jurnal berhasil disimpan', 201);
    }

    /**
     * Detail jurnal
     */
    public function show($id)
    {
        $journal = DailyJournal::where('user_id', auth()->id())
                    ->where('journal_id', $id)
                    ->first();

        if (!$journal) {
            return $this->notFoundResponse('Jurnal tidak ditemukan');
        }

        return $this->successResponse($journal, 'Detail jurnal');
    }

    /**
     * Update jurnal
     */
    public function update(Request $request, $id)
    {
        $journal = DailyJournal::where('user_id', auth()->id())
                    ->where('journal_id', $id)
                    ->first();

        if (!$journal) {
            return $this->notFoundResponse('Jurnal tidak ditemukan');
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string|min:10',
            'mood_before' => 'sometimes|integer|min:1|max:10',
            'mood_after' => 'sometimes|integer|min:1|max:10',
            'journal_date' => 'sometimes|date|before_or_equal:today'
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        $journal->update($request->only(['title', 'content', 'mood_before', 'mood_after', 'journal_date']));

        return $this->successResponse($journal, 'Jurnal berhasil diupdate');
    }

    /**
     * Hapus jurnal
     */
    public function destroy($id)
    {
        $journal = DailyJournal::where('user_id', auth()->id())
                    ->where('journal_id', $id)
                    ->first();

        if (!$journal) {
            return $this->notFoundResponse('Jurnal tidak ditemukan');
        }

        $journal->delete();

        return $this->successResponse(null, 'Jurnal berhasil dihapus');
    }

    /**
     * Analisis perubahan mood
     */
    private function analyzeMoodChange($change)
    {
        if ($change > 3) return 'Mood sangat membaik setelah menulis! ✨';
        if ($change > 0) return 'Mood sedikit membaik 📈';
        if ($change == 0) return 'Mood tetap stabil ➡️';
        if ($change >= -3) return 'Mood sedikit menurun 📉';
        return 'Mood cukup menurun. Mungkin perlu aktivitas menyenangkan lainnya 💙';
    }
}