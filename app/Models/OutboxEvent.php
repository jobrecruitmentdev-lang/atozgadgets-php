<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OutboxEvent extends Model
{
    protected $guarded = ['id'];
    protected $casts = [
        'payload' => 'array',
        'attempts' => 'integer',
    ];
}
