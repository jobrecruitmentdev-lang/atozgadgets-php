<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FulfillmentAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'fulfillment_id',
        'idempotency_key',
        'attempt_number',
        'status',
        'request_hash',
        'response_payload',
        'error_message',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function fulfillment(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Fulfillment::class);
    }
}
