<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mood_history', function (Blueprint $table) {
            $table->id('history_id');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('checkin_date');
            $table->string('overall_mood');   // Mood Baik, Stres Ringan, dll.
            $table->integer('mood_score');    // total skor
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mood_history');
    }
};