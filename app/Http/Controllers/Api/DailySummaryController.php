<?php

namespace App\Http\Controllers\Api;

use App\Models\MoodCheckin;
use App\Models\DailyJournal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DailySummaryController extends BaseController
{
    /**
     * GET: Ringkasan harian user
     * Endpoint: GET /api/daily-summary
     */
    public function getSummary(Request $request)
    {
        $user = $request->user();
        $today = now()->toDateString();

        // 1. Ambil mood check hari ini
        $todayMoodCheck = MoodCheckin::where('user_id', $user->id)
                            ->whereDate('checkin_date', $today)
                            ->latest()
                            ->first();

        // 2. Ambil jurnal hari ini
        $todayJournals = DailyJournal::where('user_id', $user->id)
                            ->whereDate('journal_date', $today)
                            ->get();

        // 3. Ambil mood check minggu ini
        $weekMoodChecks = MoodCheckin::where('user_id', $user->id)
                            ->whereBetween('checkin_date', [now()->subDays(7)->toDateString(), $today])
                            ->orderBy('checkin_date', 'asc')
                            ->get();

        // 4. Hitung statistik
        $stats = $this->calculateStats($todayMoodCheck, $todayJournals, $weekMoodChecks);

        // 5. Buat kesimpulan
        $conclusion = $this->generateConclusion($stats);

        // 6. Rekomendasi aktivitas
        $recommendations = $this->getRecommendations($stats);

        return $this->successResponse([
            'tanggal' => $today,
            'user_name' => $user->name,
            'mood_check' => $todayMoodCheck ? [
                'skor' => $todayMoodCheck->mood_score,
                'diagnosis' => $todayMoodCheck->overall_mood,
                'waktu' => $todayMoodCheck->created_at->format('H:i')
            ] : null,
            'jurnal' => [
                'total_hari_ini' => $todayJournals->count(),
                'rata_mood_sebelum' => $todayJournals->avg('mood_before') ?? 0,
                'rata_mood_sesudah' => $todayJournals->avg('mood_after') ?? 0,
                'mood_berubah' => $todayJournals->isNotEmpty() ? 
                    round($todayJournals->avg('mood_after') - $todayJournals->avg('mood_before'), 1) : 0
            ],
            'statistik' => $stats,
            'kesimpulan' => $conclusion,
            'rekomendasi' => $recommendations,
            'quote_harian' => $this->getDailyQuote($stats['mood_status'])
        ], 'Ringkasan harian berhasil diambil');
    }

    /**
     * Hitung statistik
     */
    private function calculateStats($todayMoodCheck, $todayJournals, $weekMoodChecks)
    {
        $todayMood = $todayMoodCheck ? $todayMoodCheck->mood_score : null;
        $weekAvg = $weekMoodChecks->avg('mood_score') ?? 0;
        
        // Status mood
        if ($todayMood === null) {
            $moodStatus = 'belum_cek';
        } elseif ($todayMood <= 15) {
            $moodStatus = 'baik';
        } elseif ($todayMood <= 25) {
            $moodStatus = 'ringan';
        } elseif ($todayMood <= 35) {
            $moodStatus = 'sedang';
        } else {
            $moodStatus = 'berat';
        }

        // Hitung streak (berapa hari berturut-turut cek mood)
        $streak = $this->calculateStreak($weekMoodChecks);

        // Jurnal hari ini
        $journaling = $todayJournals->isNotEmpty();
        $moodImproved = $todayJournals->isNotEmpty() && 
                       ($todayJournals->avg('mood_after') > $todayJournals->avg('mood_before'));

        return [
            'mood_skor' => $todayMood,
            'mood_status' => $moodStatus,
            'mood_emoji' => $this->getMoodEmoji($moodStatus),
            'streak_hari' => $streak,
            'rata_mingguan' => round($weekAvg, 1),
            'sudah_jurnal' => $journaling,
            'mood_membaik' => $moodImproved,
            'total_check_minggu_ini' => $weekMoodChecks->count(),
            'hari_ini_ada_mood_check' => $todayMoodCheck !== null
        ];
    }

    /**
     * Buat kesimpulan
     */
    private function generateConclusion($stats)
    {
        if ($stats['mood_status'] === 'belum_cek') {
            return "Hai! Kamu belum melakukan Mood Check hari ini. Yuk cek mood kamu sekarang biar aku bisa kasih ringkasan yang lebih personal! 🌟";
        }

        $conclusions = [
            'baik' => "Mood kamu hari ini dalam kondisi **BAIK**! ${stats['mood_emoji']}\n\nPertahankan ya! " . 
                     ($stats['sudah_jurnal'] ? "Kamu juga sudah menulis jurnal hari ini, bagus untuk kesehatan mental!" : "Coba tulis jurnal untuk merekam hari baikmu."),
            
            'ringan' => "Mood kamu terpantau **STRES RINGAN**. ${stats['mood_emoji']}\n\n" .
                       ($stats['mood_membaik'] ? "Tapi tenang, mood kamu membaik setelah menulis jurnal! " : "") .
                       "Coba lakukan aktivitas relaksasi ringan seperti mendengarkan musik atau jalan santai.",
            
            'sedang' => "Mood kamu menunjukkan **STRES SEDANG**. ${stats['mood_emoji']}\n\n" .
                       ($stats['sudah_jurnal'] ? "Bagus kamu sudah menulis jurnal! " : "Coba tulis jurnal untuk mencurahkan isi hati. ") .
                       "Pertimbangkan untuk meditasi atau bicara dengan teman terdekat.",
            
            'berat' => "Mood kamu terindikasi **STRES BERAT**. ${stats['mood_emoji']}\n\n" .
                      "Jangan ragu untuk mencari bantuan profesional ya. Kamu tidak sendiri. " .
                      ($stats['sudah_jurnal'] ? "Menulis jurnal bisa membantu, tapi konsultasi ke psikolog sangat disarankan." : "")
        ];

        return $conclusions[$stats['mood_status']] ?? '';
    }

    /**
     * Rekomendasi aktivitas
     */
    private function getRecommendations($stats)
    {
        $allRecommendations = [
            'baik' => [
                ['aktivitas' => 'Jalan pagi', 'durasi' => '30 menit', 'icon' => '🌅'],
                ['aktivitas' => 'Baca buku', 'durasi' => '20 menit', 'icon' => '📚'],
                ['aktivitas' => 'Meditasi ringan', 'durasi' => '10 menit', 'icon' => '🧘'],
            ],
            'ringan' => [
                ['aktivitas' => 'Pernapasan 4-7-8', 'durasi' => '5 menit', 'icon' => '🌬️'],
                ['aktivitas' => 'Stretching', 'durasi' => '15 menit', 'icon' => '🤸'],
                ['aktivitas' => 'Dengar musik favorit', 'durasi' => '30 menit', 'icon' => '🎵'],
            ],
            'sedang' => [
                ['aktivitas' => 'Olahraga ringan', 'durasi' => '30 menit', 'icon' => '🏃'],
                ['aktivitas' => 'Tulis jurnal', 'durasi' => '15 menit', 'icon' => '📝'],
                ['aktivitas' => 'Ngobrol dengan teman', 'durasi' => '30 menit', 'icon' => '💬'],
            ],
            'berat' => [
                ['aktivitas' => 'Konsultasi psikolog', 'durasi' => 'Sesuai jadwal', 'icon' => '👨‍⚕️'],
                ['aktivitas' => 'Meditasi terpandu', 'durasi' => '20 menit', 'icon' => '🧘'],
                ['aktivitas' => 'Istirahat cukup', 'durasi' => '7-8 jam', 'icon' => '😴'],
            ],
            'belum_cek' => [
                ['aktivitas' => 'Mood Check dulu ya!', 'durasi' => '2 menit', 'icon' => '📋'],
            ]
        ];

        return $allRecommendations[$stats['mood_status']] ?? [];
    }

    /**
     * Hitung streak
     */
    private function calculateStreak($weekMoodChecks)
    {
        $streak = 0;
        $today = now();
        
        for ($i = 0; $i < 7; $i++) {
            $date = $today->copy()->subDays($i)->toDateString();
            $check = $weekMoodChecks->where('checkin_date', $date)->first();
            
            if ($check) {
                $streak++;
            } else if ($i > 0) {
                break;
            }
        }
        
        return $streak;
    }

    /**
     * Emoji berdasarkan mood
     */
    private function getMoodEmoji($status)
    {
        return [
            'baik' => '😊',
            'ringan' => '😐',
            'sedang' => '😟',
            'berat' => '😢',
            'belum_cek' => '🤔'
        ][$status] ?? '🤔';
    }

    /**
     * Quote harian
     */
    private function getDailyQuote($moodStatus)
    {
        $quotes = [
            'baik' => '"Kebahagiaan adalah pilihan. Hari ini kamu memilih untuk bahagia." ✨',
            'ringan' => '"Stres itu wajar. Yang penting bagaimana kita menghadapinya." 💪',
            'sedang' => '"Tidak apa-apa untuk tidak baik-baik saja. Beri dirimu waktu." 🫂',
            'berat' => '"Badai pasti berlalu. Kamu lebih kuat dari yang kamu kira." 🌈',
            'belum_cek' => '"Langkah pertama untuk memahami diri sendiri adalah menyadarinya." 🌟'
        ];

        return $quotes[$moodStatus] ?? $quotes['belum_cek'];
    }

    /**
     * POST: Analisis AI (pakai OpenAI)
     * Endpoint: POST /api/daily-summary/ai-analysis
     */
    public function aiAnalysis(Request $request)
    {
        $user = $request->user();
        $today = now()->toDateString();

        // Kumpulkan data user
        $moodCheck = MoodCheckin::where('user_id', $user->id)
                        ->whereDate('checkin_date', $today)
                        ->latest()
                        ->first();

        $journals = DailyJournal::where('user_id', $user->id)
                        ->whereDate('journal_date', $today)
                        ->get();

        $weekMoods = MoodCheckin::where('user_id', $user->id)
                        ->whereBetween('checkin_date', [now()->subDays(7)->toDateString(), $today])
                        ->get();

        // Buat prompt untuk AI
        $prompt = $this->buildAIPrompt($user, $moodCheck, $journals, $weekMoods);

        // Panggil OpenAI
        $aiResponse = $this->callOpenAI($prompt);

        return $this->successResponse([
            'ai_analysis' => $aiResponse,
            'data_digunakan' => [
                'mood_check_hari_ini' => $moodCheck ? $moodCheck->only(['mood_score', 'overall_mood']) : null,
                'total_jurnal' => $journals->count(),
                'total_check_minggu_ini' => $weekMoods->count()
            ]
        ], 'Analisis AI berhasil');
    }

    /**
     * Bikin prompt untuk AI
     */
    private function buildAIPrompt($user, $moodCheck, $journals, $weekMoods)
    {
        $prompt = "Kamu adalah asisten kesehatan mental profesional untuk aplikasi MindCare.\n\n";
        $prompt .= "Berikan analisis personal dan saran untuk user berikut:\n";
        $prompt .= "- Nama: {$user->name}\n";
        $prompt .= "- Umur: {$user->umur}\n";
        $prompt .= "- Gender: {$user->gender}\n\n";

        if ($moodCheck) {
            $prompt .= "MOOD CHECK HARI INI:\n";
            $prompt .= "- Skor: {$moodCheck->mood_score}\n";
            $prompt .= "- Diagnosis: {$moodCheck->overall_mood}\n\n";
        } else {
            $prompt .= "User belum melakukan mood check hari ini.\n\n";
        }

        if ($journals->isNotEmpty()) {
            $prompt .= "JURNAL HARI INI:\n";
            $prompt .= "- Total: {$journals->count()} jurnal\n";
            $prompt .= "- Rata mood sebelum: " . round($journals->avg('mood_before'), 1) . "\n";
            $prompt .= "- Rata mood sesudah: " . round($journals->avg('mood_after'), 1) . "\n";
            $prompt .= "- Isi jurnal terakhir: \"" . $journals->last()->content . "\"\n\n";
        }

        if ($weekMoods->isNotEmpty()) {
            $prompt .= "TREN MINGGU INI:\n";
            $prompt .= "- Total check: {$weekMoods->count()}\n";
            $prompt .= "- Rata skor: " . round($weekMoods->avg('mood_score'), 1) . "\n";
        }

        $prompt .= "\nTolong berikan:\n";
        $prompt .= "1. Analisis singkat kondisi user (2-3 kalimat)\n";
        $prompt .= "2. Saran personal untuk hari ini\n";
        $prompt .= "3. Satu tantangan kecil untuk meningkatkan mood\n";
        $prompt .= "4. Rekomendasi aktivitas spesifik\n\n";
        $prompt .= "Gunakan bahasa Indonesia yang ramah dan suportif. Maksimal 300 kata.";

        return $prompt;
    }

    /**
     * Panggil OpenAI API
     */
    private function callOpenAI($prompt)
    {
        $apiKey = env('OPENAI_API_KEY');

        if (!$apiKey) {
            return "Maaf, fitur AI sedang tidak tersedia. Tapi aku tetap bisa kasih saran manual kok! 😊\n\n" .
                   "Coba lakukan aktivitas yang kamu suka hari ini, dan jangan lupa tulis jurnal ya!";
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Kamu adalah psikolog profesional yang empatik untuk aplikasi MindCare. Berikan analisis dalam Bahasa Indonesia.'
                    ],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'max_tokens' => 500,
                'temperature' => 0.7
            ]);

            if ($response->successful()) {
                return $response->json()['choices'][0]['message']['content'];
            }

            return "Maaf, AI sedang sibuk. Ini saran manual dariku: Jaga kesehatan mentalmu dengan istirahat cukup dan lakukan hal yang kamu suka! 💙";
        } catch (\Exception $e) {
            return "Maaf, layanan AI sedang tidak tersedia. Coba lagi nanti ya! 😊";
        }
    }
}