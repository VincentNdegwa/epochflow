<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = CartItem::with('product.images')
            ->where('user_id', auth()->id())
            ->get();

        $total = $cartItems->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });

        return Inertia::render('Cart/Index', [
            'cartItems' => $cartItems,
            'total' => $total
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $validated['user_id'] = auth()->id();

        // Check if product is already in cart
        $cartItem = CartItem::where('user_id', auth()->id())
            ->where('product_id', $validated['product_id'])
            ->first();

        if ($cartItem) {
            $cartItem->update([
                'quantity' => $cartItem->quantity + $validated['quantity']
            ]);
        } else {
            CartItem::create($validated);
        }

        return redirect()->back()
            ->with('success', 'Product added to cart successfully.');
    }

    public function update(Request $request, CartItem $cartItem)
    {
        \Illuminate\Support\Facades\Gate::authorize('update', $cartItem);

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cartItem->update($validated);

        return redirect()->back()
            ->with('success', 'Cart updated successfully.');
    }

    public function destroy(CartItem $cartItem)
    {
        \Illuminate\Support\Facades\Gate::authorize('delete', $cartItem);

        $cartItem->delete();

        return redirect()->back()
            ->with('success', 'Item removed from cart.');
    }
}
