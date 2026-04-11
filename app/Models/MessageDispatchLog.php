<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageDispatchLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_id',
        'patient_user_id',
        'sender_user_id',
        'template_id',
        'category',
        'channel',
        'stage',
        'trigger_mode',
        'status',
        'recipient',
        'reason',
        'provider_response',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];
}
