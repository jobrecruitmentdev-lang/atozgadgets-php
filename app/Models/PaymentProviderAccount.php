<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class PaymentProviderAccount extends Model
{
    protected $guarded = ['id'];
    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];

    public function setClientSecretAttribute($value)
    {
        $this->attributes['client_secret_encrypted'] = !empty($value) ? Crypt::encryptString($value) : null;
    }

    public function getClientSecretAttribute()
    {
        if (empty($this->attributes['client_secret_encrypted'])) {
            return null;
        }
        try {
            return Crypt::decryptString($this->attributes['client_secret_encrypted']);
        } catch (\Throwable $e) {
            return null;
        }
    }
}
