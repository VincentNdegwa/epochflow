<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\Customer;

class OrderPolicy
{
    public function view(Customer $customer, Order $order): bool
    {
        return $customer->id === $order->customer_id;
    }

    public function update(Customer $customer, Order $order): bool
    {
        return $customer->id === $order->customer_id;
    }
}
