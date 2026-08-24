<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiskAssessment extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];
    protected $casts = [
        'risk_score' => 'integer',
        'signals' => 'array',
        'created_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
