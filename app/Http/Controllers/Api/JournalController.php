<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DailyJournal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JournalController extends Controller
{
    /**
     * GET: Lihat semua jurnal user yang login
     */
    public function index(Request $request)
    {
        $journals = DailyJournal::where('user_id', $request->user()->id)
                    ->orderBy('journal_date', 'desc')
                    ->get();

        return response()->json([
            'status' => true,
            'message' => 'Daftar jurnal harian',
            'data' => $journals
        ], 200);
    }

    /**
     * POST: Buat jurnal baru
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'mood_before' => 'required|integer|min:1|max:10',
            'mood_after' => 'required|integer|min:1|max:10',
            'journal_date' => 'required|date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $journal = DailyJournal::create([
            'user_id' => $request->user()->id,
            'title' => $request->title,
            'content' => $request->content,
            'mood_before' => $request->mood_before,
            'mood_after' => $request->mood_after,
            'journal_date' => $request->journal_date
        ]);

        // Hitung perubahan mood
        $moodChange = $request->mood_after - $request->mood_before;
        $moodStatus = $moodChange > 0 ? 'Mood membaik' : ($moodChange < 0 ? 'Mood menurun' : 'Mood tetap');

        return response()->json([
            'status' => true,
            'message' => 'Jurnal berhasil disimpan',
            'data' => [
                'journal' => $journal,
                'mood_change' => $moodChange,
                'mood_status' => $moodStatus
            ]
        ], 201);
    }

    /**
     * GET: Lihat detail satu jurnal
     */
    public function show($id)
    {
        $journal = DailyJournal::where('user_id', auth()->id())
                    ->where('journal_id', $id)
                    ->first();

        if (!$journal) {
            return response()->json([
                'status' => false,
                'message' => 'Jurnal tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $journal
        ], 200);
    }

    /**
     * PUT: Update jurnal
     */
    public function update(Request $request, $id)
    {
        $journal = DailyJournal::where('user_id', auth()->id())
                    ->where('journal_id', $id)
                    ->first();

        if (!$journal) {
            return response()->json([
                'status' => false,
                'message' => 'Jurnal tidak ditemukan'
            ], 404);
        }

        $journal->update($request->only(['title', 'content', 'mood_before', 'mood_after', 'journal_date']));

        return response()->json([
            'status' => true,
            'message' => 'Jurnal berhasil diupdate',
            'data' => $journal
        ], 200);
    }

    /**
     * DELETE: Hapus jurnal
     */
    public function destroy($id)
    {
        $journal = DailyJournal::where('user_id', auth()->id())
                    ->where('journal_id', $id)
                    ->first();

        if (!$journal) {
            return response()->json([
                'status' => false,
                'message' => 'Jurnal tidak ditemukan'
            ], 404);
        }

        $journal->delete();

        return response()->json([
            'status' => true,
            'message' => 'Jurnal berhasil dihapus'
        ], 200);
    }
}