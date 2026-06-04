<?php

namespace App\Http\Controllers\Api;

use App\Models\ChatbotMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class ChatbotController extends BaseController
{
    /**
     * Kirim pesan ke chatbot (AI + fallback)
     */
    public function sendMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:1000'
        ], [
            'message.max' => 'Pesan maksimal 1000 karakter'
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        $userMessage = $request->message;
        $userId = $request->user()->id;

        // Simpan pesan user (dapatkan instance untuk ID asli)
        $userMsg = ChatbotMessage::create([
            'user_id' => $userId,
            'sender' => 'user',
            'message_text' => $userMessage
        ]);

        // Dapatkan respons (coba AI dulu, jika gagal pakai fallback)
        $botResponse = $this->getAIResponse($userMessage, $request->user()->name);

        // Simpan pesan bot
        $botMsg = ChatbotMessage::create([
            'user_id' => $userId,
            'sender' => 'bot',
            'message_text' => $botResponse
        ]);

        return $this->successResponse([
            'user_message' => [
                'message_id' => $userMsg->message_id,
                'message_text' => $userMessage
            ],
            'bot_response' => [
                'message_id' => $botMsg->message_id,
                'message_text' => $botResponse
            ]
        ], 'Pesan terkirim', 201);
    }

    /**
     * Riwayat chat user
     */
    public function getHistory(Request $request)
    {
        $messages = ChatbotMessage::where('user_id', $request->user()->id)
                    ->orderBy('created_at', 'asc')
                    ->get();

        return $this->successResponse([
            'total_messages' => $messages->count(),
            'messages' => $messages
        ], 'Riwayat chat');
    }

    /**
     * Mendapatkan respons dari OpenAI, atau fallback ke lokal
     */
    private function getAIResponse(string $message, string $userName): string
    {
        $apiKey = env('OPENAI_API_KEY');

        if ($apiKey) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type'  => 'application/json',
                ])->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => $this->getSystemPrompt($userName)
                        ],
                        [
                            'role' => 'user',
                            'content' => $message
                        ]
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 400,
                ]);

                if ($response->successful()) {
                    return $response->json()['choices'][0]['message']['content'];
                }
            } catch (\Exception $e) {
                // Jika gagal, lanjut ke fallback
            }
        }

        // Fallback ke respons lokal
        return $this->getFallbackResponse($message);
    }

    /**
     * Prompt sistem untuk OpenAI agar fokus pada kesehatan mental
     */
    private function getSystemPrompt(string $userName): string
    {
        return "Kamu adalah asisten kesehatan mental di aplikasi MindCare. Nama pengguna adalah {$userName}. "
            . "Bersikaplah ramah, empatik, dan suportif. Kamu boleh memberikan informasi umum seputar kesehatan mental, "
            . "tips mengelola stres, kecemasan, depresi, dan kebiasaan sehat. Jangan memberikan diagnosis medis atau resep obat. "
            . "Jika pengguna menunjukkan gejala serius, sarankan untuk menemui profesional. Gunakan bahasa Indonesia yang santai dan mudah dimengerti. "
            . "Jawablah maksimal dalam 4 kalimat.";
    }

    /**
     * Respons lokal sebagai cadangan (lebih bervariasi dan informatif)
     */
    private function getFallbackResponse(string $message): string
    {
        $msg = strtolower($message);

        // Basis data respons lokal yang lebih kaya
        $responses = [
            'stres' => [
                "Stres itu wajar, kok. Coba teknik 4-7-8: tarik napas 4 detik, tahan 7 detik, hembuskan 8 detik. Lakukan 3 kali, pasti lebih tenang.",
                "Untuk mengurangi stres, kamu bisa coba journaling atau sekadar menulis apa yang kamu rasakan. Mau aku bantu buat jurnal sekarang?",
                "Stres bisa dikelola dengan istirahat cukup dan olahraga ringan. Jalan kaki 15 menit saja sudah membantu."
            ],
            'cemas|anxiety|gelisah' => [
                "Cemas itu alarm alami tubuh. Coba teknik grounding 5-4-3-2-1: sebutkan 5 benda di sekitarmu, 4 suara, 3 sentuhan, 2 bau, 1 rasa. Dijamin lebih fokus.",
                "Kalau cemas, tarik napas panjang dan ingatkan diri sendiri bahwa perasaan ini akan berlalu. Kamu aman."
            ],
            'sedih|sedang sedih|down' => [
                "Gak apa-apa merasa sedih. Itu manusiawi. Kalau mau cerita, aku siap mendengarkan.",
                "Saat sedih, coba lakukan hal kecil yang biasanya kamu suka: dengerin lagu favorit, lihat foto lucu, atau telepon teman."
            ],
            'capek|lelah|burnout' => [
                "Kayaknya kamu butuh istirahat. Tidur cukup (7-8 jam) bisa memperbaiki mood. Matikan dulu HP dan coba rebahan tanpa gangguan.",
                "Lelah mental itu nyata. Jangan memaksakan diri. Mungkin kamu bisa ambil cuti sehari untuk 'me time'."
            ],
            'tidur|insomnia|susah tidur' => [
                "Susah tidur? Coba kurangi kafein setelah jam 3 sore, dan matikan layar 1 jam sebelum tidur. Membaca buku juga bisa membantu.",
                "Rutinitas tidur yang konsisten sangat penting. Coba tidur dan bangun di jam yang sama setiap hari."
            ],
            'sendiri|kesepian|lonely' => [
                "Merasa sendiri itu berat. Tapi ingat, ada orang-orang yang peduli padamu. Coba hubungi teman lama atau keluarga.",
                "Kesepian bisa diatasi dengan bergabung di komunitas atau melakukan hobi baru. Kamu tidak sendiri."
            ],
            'overthinking|banyak pikiran' => [
                "Overthinking sering memperburuk keadaan. Coba tulis semua pikiranmu di kertas, lalu lihat lagi dengan objektif. Biasanya tidak separah yang dibayangkan.",
                "Saat pikiran berkecamuk, fokus ke hal yang bisa kamu kontrol. Sisanya, biarkan berlalu."
            ],
        ];

        // Cari kecocokan kata kunci
        foreach ($responses as $keywords => $options) {
            $keywordArray = explode('|', $keywords);
            foreach ($keywordArray as $keyword) {
                if (strpos($msg, trim($keyword)) !== false) {
                    return $options[array_rand($options)];
                }
            }
        }

        // Respons default (lebih bervariasi)
        $defaults = [
            "Terima kasih sudah berbagi. 💙 Ingat, menjaga kesehatan mental sama pentingnya dengan kesehatan fisik. Ada yang bisa aku bantu?",
            "Aku di sini untuk mendengarkan. Ceritakan apa yang kamu rasakan, kadang berbagi bisa meringankan beban.",
            "Setiap orang punya hari baik dan buruk. Tidak apa-apa untuk tidak baik-baik saja. Mau cerita lebih lanjut?",
        ];

        return $defaults[array_rand($defaults)];
    }
}