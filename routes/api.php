<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MoodCheckController;
use App\Http\Controllers\Api\JournalController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\ChatbotController;

// PUBLIC ROUTES
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// PROTECTED ROUTES (harus login)
Route::middleware('auth:sanctum')->group(function () {
    
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Mood Check
    Route::get('/mood-questions', [MoodCheckController::class, 'getQuestions']);
    Route::post('/mood-check', [MoodCheckController::class, 'submitMoodCheck']);
    Route::get('/mood-history', [MoodCheckController::class, 'getHistory']); // ← TAMBAHAN BARU
    
    // Jurnal
    Route::apiResource('/journals', JournalController::class);
    
    // Chatbot
    Route::post('/chatbot/send', [ChatbotController::class, 'sendMessage']);
    Route::get('/chatbot/history', [ChatbotController::class, 'getHistory']);
});

// Artikel (public)
Route::get('/articles', [ArticleController::class, 'index']);
Route::get('/articles/{id}', [ArticleController::class, 'show']);