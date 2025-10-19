<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentIntegration extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider',      
        'provider_id',   
        'store_id',      
        'status',        
        'metadata',      
        'credentials',   
        'is_configured', 
        'is_enabled'     
    ];

    protected $casts = [
        'metadata' => 'array',
        'credentials' => 'encrypted:array',
        'is_configured' => 'boolean',
        'is_enabled' => 'boolean'
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}