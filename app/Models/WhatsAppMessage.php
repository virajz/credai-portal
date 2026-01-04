<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsAppMessage extends Model
{
    protected $table = 'whatsapp_messages';

    protected $fillable = [
        'message_id',
        'channel',
        'from',
        'to',
        'sender_name',
        'content_type',
        'text',
        'raw_payload',
        'received_at',
        'thank_you_sent',
    ];

    protected function casts(): array
    {
        return [
            'raw_payload' => 'array',
            'received_at' => 'datetime',
            'thank_you_sent' => 'boolean',
        ];
    }

    /**
     * Check if this is the first message from this sender
     */
    public static function isFirstMessageFrom(string $phoneNumber): bool
    {
        return ! self::where('from', $phoneNumber)->exists();
    }
}
