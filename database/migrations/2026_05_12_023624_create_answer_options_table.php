<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::create('answer_options', function (Blueprint $table) {
        $table->id('option_id');
        $table->foreignId('question_id')->constrained('mood_questions', 'question_id')->onDelete('cascade');
        $table->string('option_text'); // "Sangat Tidak Sesuai", "Tidak Sesuai", "Sesuai", "Sangat Sesuai"
        $table->integer('score_value'); // 1, 2, 3, 4
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('answer_options');
    }
};
