<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DjangoWebhookEvent extends Model
{
    public $timestamps = false;

    protected $table = 'django_webhook_events';

    protected $fillable = [
        'event_id',
        'event_type',
        'received_at',
        'processed_at',
        'status',
        'payload_json',
        'error_message',
    ];

    protected $casts = [
        'payload_json' => 'array',
        'received_at'  => 'datetime',
        'processed_at' => 'datetime',
    ];
}
