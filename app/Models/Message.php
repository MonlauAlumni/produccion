<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'user_id',
        'content',
        'read_at'
    ];

    protected $casts = [
        'read_at' => 'datetime'
    ];

    /**
     * Obtener la conversación a la que pertenece el mensaje
     */
    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * Obtener el usuario que envió el mensaje
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}