<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'recipient_id',
        'job_offer_id'
    ];

    /**
     * Obtener el usuario que inició la conversación
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Obtener el destinatario de la conversación
     */
    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    /**
     * Obtener la oferta de trabajo relacionada (si existe)
     */
    public function jobOffer()
    {
        return $this->belongsTo(JobOffer::class, 'job_offer_id');
    }

    /**
     * Obtener todos los mensajes de la conversación
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Obtener el último mensaje de la conversación
     */
    public function lastMessage()
    {
        return $this->hasOne(Message::class)->latest();
    }
}