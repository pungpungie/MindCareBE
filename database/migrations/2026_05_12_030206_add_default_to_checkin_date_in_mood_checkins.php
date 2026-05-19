<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mood_checkins', function (Blueprint $table) {
            $table->date('checkin_date')->default(now()->toDateString())->change();
        });
    }

    public function down(): void
    {
        Schema::table('mood_checkins', function (Blueprint $table) {
            $table->date('checkin_date')->default(null)->change();
        });
    }
};