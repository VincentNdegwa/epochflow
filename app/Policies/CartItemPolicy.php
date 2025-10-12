<?php

namespace App\Policies;

use App\Models\CartItem;
use App\Models\Customer;
use App\Models\User;

class CartItemPolicy
{
    public function update(Customer $customer, CartItem $cartItem): bool
    {
        return $customer->id === $cartItem->customer_id;
    }

    public function delete(Customer $customer, CartItem $cartItem): bool
    {
        return $customer->id === $cartItem->customer_id;
    }
}
