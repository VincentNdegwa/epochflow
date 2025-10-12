<?php

namespace App\Providers;

use App\Models\Product;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Store;
use App\Policies\ProductPolicy;
use App\Policies\CartItemPolicy;
use App\Policies\OrderPolicy;
use App\Policies\StorePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Product::class => ProductPolicy::class,
        CartItem::class => CartItemPolicy::class,
        Order::class => OrderPolicy::class,
        Store::class => StorePolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
