<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IdempotencyRecord extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];
    protected $casts = [
        'response_body' => 'array',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
    ];
}
