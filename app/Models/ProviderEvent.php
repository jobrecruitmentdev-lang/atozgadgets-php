<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProviderEvent extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];
    protected $casts = [
        'payload' => 'array',
        'signature_verified' => 'boolean',
        'processed_at' => 'datetime',
        'created_at' => 'datetime',
    ];
}
