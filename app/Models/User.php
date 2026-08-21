<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'role_id',
        'first_name',
        'last_name',
        'email',
        'mobile',
        'password',
        'password_hash',
        'profile_image',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'password_hash',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Check if the database table has password_hash column.
     *
     * @return bool
     */
    private function hasPasswordHashColumn()
    {
        static $hasColumn = null;
        if ($hasColumn === null) {
            try {
                $hasColumn = \Illuminate\Support\Facades\Schema::hasColumn($this->getTable(), 'password_hash');
            } catch (\Exception $e) {
                $hasColumn = false;
            }
        }
        return $hasColumn;
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->hasPasswordHashColumn() ? ($this->attributes['password_hash'] ?? null) : ($this->attributes['password'] ?? null);
    }

    /**
     * Set the password attribute.
     *
     * @param  string  $value
     * @return void
     */
    public function setPasswordAttribute($value)
    {
        if ($this->hasPasswordHashColumn()) {
            $this->attributes['password_hash'] = $value;
        } else {
            $this->attributes['password'] = $value;
        }
    }

    /**
     * Get the password attribute.
     *
     * @return string
     */
    public function getPasswordAttribute()
    {
        return $this->hasPasswordHashColumn() ? ($this->attributes['password_hash'] ?? null) : ($this->attributes['password'] ?? null);
    }

    /**
     * Get the orders for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    /**
     * Get the saved addresses for the user.
     */
    public function addresses(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Address::class, 'user_id');
    }

    /**
     * Get the product reviews written by the user.
     */
    public function reviews(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProductReview::class, 'user_id');
    }
}
