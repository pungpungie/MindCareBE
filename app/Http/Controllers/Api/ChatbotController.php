<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatbotMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class ChatbotController extends Controller
{
    /**
     * POST: Kirim pesan ke AI
     */
    public function sendMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Simpan pesan user
        $userMessage = ChatbotMessage::create([
            'user_id' => $request->user()->id,
            'sender' => 'user',
            'message_text' => $request->message
        ]);

        // Panggil AI (OpenAI atau fallback)
        $aiResponse = $this->getAIResponse($request->message);

        // Simpan respon bot
        $botMessage = ChatbotMessage::create([
            'user_id' => $request->user()->id,
            'sender' => 'bot',
            'message_text' => $aiResponse
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Pesan terkirim',
            'data' => [
                'user_message' => $userMessage,
                'bot_response' => $botMessage
            ]
        ], 201);
    }

    /**
     * GET: Ambil history chat user
     */
    public function getHistory(Request $request)
    {
        $messages = ChatbotMessage::where('user_id', $request->user()->id)
                    ->orderBy('created_at', 'asc')
                    ->get();

        return response()->json([
            'status' => true,
            'message' => 'Riwayat chat',
            'data' => $messages
        ], 200);
    }

    /**
     * Fungsi untuk dapat response dari AI
     */
    private function getAIResponse($message)
    {
        // Coba panggil OpenAI jika API key tersedia
        $apiKey = env('OPENAI_API_KEY');

        if ($apiKey) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ])->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Kamu adalah asisten kesehatan mental bernama MindCare. Bersikaplah empatik, suportif, dan berikan saran yang membantu. Jangan memberikan diagnosis medis, selalu sarankan untuk konsultasi ke profesional jika diperlukan.'
                        ],
                        [
                            'role' => 'user',
                            'content' => $message
                        ]
                    ],
                    'max_tokens' => 500,
                    'temperature' => 0.7
                ]);

                if ($response->successful()) {
                    return $response->json()['choices'][0]['message']['content'];
                }
            } catch (\Exception $e) {
                // Jika error, pakai fallback
            }
        }

        // Fallback response kalau gak ada API key
        return $this->getFallbackResponse($message);
    }

    /**
     * Response sederhana kalau gak pakai OpenAI
     */
    private function getFallbackResponse($message)
    {
        $responses = [
            'stres' => 'Saya mengerti kamu merasa stres. Coba tarik napas dalam-dalam 5 kali. Ingat, stres adalah hal yang normal. Kalau kamu mau, ceritakan lebih detail apa yang kamu rasakan.',
            'sedih' => 'Tidak apa-apa merasa sedih. Perasaan ini valid. Coba tulis jurnal tentang apa yang kamu rasakan, itu bisa membantu.',
            'capek' => 'Istirahat itu penting! Jangan memaksakan diri. Coba tidur cukup dan lakukan hal yang kamu suka.',
            'lelah' => 'Lelah itu wajar. Mungkin kamu butuh me-time. Coba jalan santai atau dengerin musik favoritmu.',
            'cemas' => 'Kecemasan bisa diatasi dengan teknik grounding. Coba sebutkan 5 hal yang kamu lihat, 4 yang kamu dengar, 3 yang kamu rasakan, 2 yang kamu cium, dan 1 yang kamu rasakan di lidah.',
            'default' => 'Terima kasih sudah berbagi. Ingat, kamu tidak sendiri. Kalau kamu merasa butuh bantuan lebih, jangan ragu untuk bicara dengan orang terdekat atau profesional. Ada yang bisa saya bantu?'
        ];

        // Cek kata kunci di pesan user
        foreach ($responses as $keyword => $response) {
            if (stripos($message, $keyword) !== false) {
                return $response;
            }
        }

        return $responses['default'];
    }
}