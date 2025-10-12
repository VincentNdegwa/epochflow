<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CartController extends Controller
{
    public function index()
    {
        $storeSlug = request()->route('storeSlug');
        $store = Store::where('slug', $storeSlug)->firstOrFail();

        $customer = Auth::guard('customer')->user();

        $cartItems = collect();
        $total = 0;

        if ($customer) {
            $cartItems = CartItem::with('product.images', 'product')
                ->where('customer_id', $customer->id)
                ->get()
                ->filter(function ($item) use ($store) {
                    return $item->product && $item->product->store_id == $store->id;
                })
                ->values();

            $total = $cartItems->sum(function ($item) {
                return $item->product->price * $item->quantity;
            });
        }

        $template = $store->template ?? 'default';

        return Inertia::render("templates/{$template}/pages/Cart/Index", [
            'cartItems' => $cartItems,
            'total' => $total,
            'customer' => $customer,
            'store' => $store,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $storeSlug = $request->route('storeSlug');
        $store = Store::where('slug', $storeSlug)->firstOrFail();

        $customer = Auth::guard('customer')->user();
        if (! $customer) {
            return redirect()->route('stores.show', ['slug' => $store->slug])
                ->with('error', 'You must be logged in as a customer to add items to cart.');
        }

        $validated['customer_id'] = $customer->id;

        $product = Product::findOrFail($validated['product_id']);
        if ($product->store_id !== $store->id) {
            abort(404);
        }

        $cartItem = CartItem::where('customer_id', $customer->id)
            ->where('product_id', $validated['product_id'])
            ->first();

        if ($cartItem) {
            $cartItem->update([
                'quantity' => $cartItem->quantity + $validated['quantity']
            ]);
        } else {
            CartItem::create($validated);
        }

        return redirect()->route('cart.index', ['storeSlug' => $store->slug])
            ->with('success', 'Product added to cart successfully.');
    }

    public function update(Request $request, $storeSlug, CartItem $cartItem)
    {
        $store = Store::where('slug', $storeSlug)->firstOrFail();

        $customer = Auth::guard('customer')->user();
        if (! $customer || $cartItem->customer_id !== $customer->id || $cartItem->product->store_id !== $store->id) {
            abort(403);
        }

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cartItem->update($validated);

        return redirect()->route('cart.index', ['storeSlug' => $store->slug])
            ->with('success', 'Cart updated successfully.');
    }

    public function destroy($storeSlug, CartItem $cartItem)
    {
        $store = Store::where('slug', $storeSlug)->firstOrFail();

        $customer = Auth::guard('customer')->user();
        if (! $customer || $cartItem->customer_id !== $customer->id || $cartItem->product->store_id !== $store->id) {
            abort(403);
        }

        $cartItem->delete();

        return redirect()->route('cart.index', ['storeSlug' => $store->slug])
            ->with('success', 'Item removed from cart.');
    }
}
