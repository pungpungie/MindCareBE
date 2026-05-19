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
    Schema::create('mood_checkins', function (Blueprint $table) {
        $table->id('checkin_id');
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->date('checkin_date');
        $table->string('overall_mood'); // "Stres Ringan", "Stres Sedang", "Stres Berat"
        $table->integer('mood_score'); // total skor
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mood_checkins');
    }
};
