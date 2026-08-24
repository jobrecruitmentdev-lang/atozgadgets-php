<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FulfillmentException extends Model
{
    use HasFactory;

    protected $fillable = [
        'fulfillment_id',
        'error_code',
        'error_message',
        'context_payload',
        'resolution_status',
        'resolved_by',
        'resolved_at',
    ];

    protected $casts = [
        'context_payload' => 'array',
        'resolved_at' => 'datetime',
    ];

    public function fulfillment(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Fulfillment::class);
    }

    public function resolver(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }
}
