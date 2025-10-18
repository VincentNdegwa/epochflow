<?php

namespace App\Models;

use App\Traits\HasEncryptedSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Customer extends Authenticatable
{
    use HasEncryptedSlug, HasFactory, Notifiable;

    protected $fillable = [
        'store_id',
        'name',
        'email',
        'password',
        'phone',
        'address',
        'billing_address',
        'billing_city',
        'billing_state',
        'billing_zip_code',
        'billing_country',
        'shipping_address',
        'shipping_city',
        'shipping_state',
        'shipping_zip_code',
        'shipping_country',
        'city',
        'state',
        'zip_code',
        'country',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = [
        'slug',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function cartItems(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CartItem::class);
    }
}
