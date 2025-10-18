<?php

namespace App\Models;

use App\Traits\HasEncryptedSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasEncryptedSlug;

    protected $fillable = [
        'store_id',
        'name',
        'description',
        'is_active',
        'image_url',
    ];

    protected $appends = [
        'slug',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
