<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected static function booted()
    {
        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-' . strtoupper(Str::random(10));
            }
        });
    }

    /**
     * Get the customer that placed the order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the line items associated with this order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    /**
     * Get the CJ Dropshipping order mapping record.
     */
    public function cjOrder(): HasOne
    {
        return $this->hasOne(CjOrder::class, 'internal_order_id');
    }

    /**
     * Get all payment transactions for this order.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'order_id');
    }

    public function paymentTransactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class, 'order_id');
    }

    /**
     * Get the latest payment for this order.
     */
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class, 'order_id')->latestOfMany();
    }

    /**
     * Get the shipment tracking record for this order.
     */
    public function shipment(): HasOne
    {
        return $this->hasOne(Shipment::class, 'order_id');
    }

    /**
     * Get the immutable frozen address snapshot for this order.
     */
    public function orderAddress(): HasOne
    {
        return $this->hasOne(OrderAddress::class, 'order_id');
    }

    /**
     * Get the supplier fulfillment order mappings for this order.
     */
    public function supplierOrders(): HasMany
    {
        return $this->hasMany(SupplierOrder::class, 'order_id');
    }

    /**
     * Dynamic structured address accessor prioritizing immutable order_addresses snapshot.
     */
    public function getAddressAttribute(): ?object
    {
        if ($this->orderAddress()->exists()) {
            $addr = $this->orderAddress;
            return (object) [
                'country' => $addr->country,
                'address_line1' => $addr->address_line1,
                'address_line2' => $addr->address_line2 ?? '',
                'postal_code' => $addr->postal_code,
                'city' => $addr->city,
                'state' => $addr->state,
                'first_name' => $addr->first_name,
                'last_name' => $addr->last_name,
                'phone' => $addr->phone,
                'email' => $addr->email,
            ];
        }

        if (!empty($this->shipping_address)) {
            $decoded = is_array($this->shipping_address) ? $this->shipping_address : json_decode($this->shipping_address, true);
            if (is_array($decoded)) {
                return (object) [
                    'country' => $decoded['country'] ?? 'US',
                    'address_line1' => $decoded['address1'] ?? ($decoded['address_line_1'] ?? ''),
                    'address_line2' => $decoded['address2'] ?? ($decoded['address_line_2'] ?? ''),
                    'postal_code' => $decoded['postal_code'] ?? ($decoded['zip'] ?? ''),
                    'city' => $decoded['city'] ?? '',
                    'state' => $decoded['state'] ?? ($decoded['province'] ?? ''),
                    'first_name' => $decoded['first_name'] ?? '',
                    'last_name' => $decoded['last_name'] ?? '',
                    'phone' => $decoded['phone'] ?? ($this->contact_phone ?? ''),
                ];
            }
        }

        if ($this->user && $this->user->addresses()->exists()) {
            $addr = $this->user->addresses()->where('is_default', true)->first() ?? $this->user->addresses()->first();
            return (object) [
                'country' => $addr->country,
                'address_line1' => $addr->address_line_1,
                'address_line2' => '',
                'postal_code' => $addr->postal_code,
                'city' => $addr->city,
                'state' => $addr->state,
                'first_name' => $this->user->first_name,
                'last_name' => $this->user->last_name,
                'phone' => $this->user->mobile,
            ];
        }

        return null;
    }
}
