<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    // Tambahkan HasApiTokens disini
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'username',
        'umur',
        'gender',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relasi: User punya banyak mood checkin
    public function moodCheckins()
    {
        return $this->hasMany(MoodCheckin::class);
    }

    // Relasi: User punya banyak jurnal
    public function dailyJournals()
    {
        return $this->hasMany(DailyJournal::class);
    }

    // Relasi: User punya banyak chat
    public function chatbotMessages()
    {
        return $this->hasMany(ChatbotMessage::class);
    }
        // Di dalam class User, tambahkan ini:
    protected static function boot()
    {
        parent::boot();
        
        // Auto logout setelah 7 hari
        static::deleting(function ($user) {
            $user->tokens()->delete();
        });
}
}