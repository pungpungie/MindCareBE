<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatbotMessage extends Model
{
    use HasFactory;

    protected $primaryKey = 'message_id';
    
    protected $fillable = [
        'user_id',
        'sender',          // ← INI HARUS ADA!
        'message_text'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}