<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MoodCheckController;
use App\Http\Controllers\Api\JournalController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\ChatbotController;
use App\Http\Controllers\Api\DailySummaryController;

// ===== PUBLIC ROUTES =====
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Artikel (bisa diakses tanpa login)
Route::get('/articles', [ArticleController::class, 'index']);
Route::get('/articles/{id}', [ArticleController::class, 'show']);

// ===== PROTECTED ROUTES (HARUS LOGIN) =====
Route::middleware('auth:sanctum')->group(function () {
    
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    
    // Mood Check
    Route::get('/mood-questions', [MoodCheckController::class, 'getQuestions']);
    Route::post('/mood-check', [MoodCheckController::class, 'submitMoodCheck']);
    Route::get('/mood-history', [MoodCheckController::class, 'getHistory']);
    
    
    // Jurnal
    Route::get('/journals', [JournalController::class, 'index']);
    Route::post('/journals', [JournalController::class, 'store']);
    Route::get('/journals/{id}', [JournalController::class, 'show']);
    Route::put('/journals/{id}', [JournalController::class, 'update']);
    Route::delete('/journals/{id}', [JournalController::class, 'destroy']);
    
    // Chatbot
    Route::post('/chatbot/send', [ChatbotController::class, 'sendMessage']);
    Route::get('/chatbot/history', [ChatbotController::class, 'getHistory']);

    // Daily Summary
    Route::get('/daily-summary', [DailySummaryController::class, 'getSummary']);
    Route::post('/daily-summary/ai-analysis', [DailySummaryController::class, 'aiAnalysis']);

    // Hapus akun
    Route::delete('/account', [AuthController::class, 'deleteAccount']);
});