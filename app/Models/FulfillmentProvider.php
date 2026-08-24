<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FulfillmentProvider extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'type',
        'enabled',
        'configuration',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'configuration' => 'array',
    ];

    public function fulfillments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Fulfillment::class, 'provider_id');
    }
}
