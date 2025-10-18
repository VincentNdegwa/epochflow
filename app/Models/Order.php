<?php

namespace App\Models;

use App\Traits\HasEncryptedSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasEncryptedSlug;

    protected $fillable = [
        'store_id',
        'customer_id',
        'order_number',
        'total_amount',
        'status',
        'notes',
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
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
    ];

    protected $appends = [
        'slug',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
