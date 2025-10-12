<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class CustomerOrderController extends Controller
{
    public function index(Request $request)
    {
        $storeSlug = $request->route('storeSlug');
        $store = Store::where('slug', $storeSlug)->firstOrFail();

        $customer = Auth::guard('customer')->user();

        $orders = Order::with('items.product')
            ->where('customer_id', $customer?->id)
            ->where('store_id', $store->id)
            ->latest()
            ->paginate(12);

        $template = $store->template ?? 'default';

        return Inertia::render("templates/{$template}/pages/Orders/Index", [
            'orders' => $orders,
            'store' => $store,
            'customer' => $customer,
        ]);
    }

    public function show(Request $request, Order $order)
    {
        $storeSlug = $request->route('storeSlug');
        $store = Store::where('slug', $storeSlug)->firstOrFail();

        $customer = Auth::guard('customer')->user();
        if (! $customer || $order->customer_id !== $customer->id || $order->store_id !== $store->id) {
            abort(403);
        }

        $template = $store->template ?? 'default';

        return Inertia::render("templates/{$template}/pages/Orders/Show", [
            'order' => $order->load('items.product'),
            'store' => $store,
            'customer' => $customer,
        ]);
    }

    public function create(Request $request)
    {
        $storeSlug = $request->route('storeSlug');
        $store = Store::where('slug', $storeSlug)->firstOrFail();

        $customer = Auth::guard('customer')->user();

        $cartItems = collect();
        $total = 0;
        if ($customer) {
            $cartItems = CartItem::with('product')
                ->where('customer_id', $customer->id)
                ->get()
                ->filter(fn($i) => $i->product && $i->product->store_id == $store->id)
                ->values();

            $total = $cartItems->sum(fn($item) => $item->product->price * $item->quantity);
        }

        $template = $store->template ?? 'default';

        return Inertia::render("templates/{$template}/pages/Checkout/Index", [
            'cartItems' => $cartItems,
            'total' => $total,
            'store' => $store,
            'customer' => $customer,
        ]);
    }

    public function store(Request $request)
    {
        $storeSlug = $request->route('storeSlug');
        $store = Store::where('slug', $storeSlug)->firstOrFail();

        $validated = $request->validate([
            'billing_address' => 'required|string',
            'billing_city' => 'required|string',
            'billing_state' => 'required|string',
            'billing_zip_code' => 'required|string',
            'billing_country' => 'required|string',
            'shipping_address' => 'required|string',
            'shipping_city' => 'required|string',
            'shipping_state' => 'required|string',
            'shipping_zip_code' => 'required|string',
            'shipping_country' => 'required|string',
            'notes' => 'nullable|string',
            'payment_method' => 'required|string',
        ]);

        $customer = Auth::guard('customer')->user();
        if (! $customer) {
            return redirect()->route('stores.show', ['slug' => $store->slug])->with('error', 'Please login as a customer to checkout.');
        }

        $cartItems = CartItem::with('product')
            ->where('customer_id', $customer->id)
            ->get()
            ->filter(fn($i) => $i->product && $i->product->store_id == $store->id)
            ->values();

        if ($cartItems->isEmpty()) {
            return redirect()->back()->with('error', 'Your cart is empty.');
        }

        // Check stock availability
        foreach ($cartItems as $item) {
            if ($item->product->stock < $item->quantity) {
                return redirect()->back()->with('error', "Product {$item->product->name} does not have enough stock.");
            }
        }

        $total = $cartItems->sum(fn($item) => $item->product->price * $item->quantity);

        $orderData = array_merge($validated, [
            'store_id' => $store->id,
            'customer_id' => $customer->id,
            'order_number' => 'ORD-' . Str::upper(Str::random(8)),
            'total_amount' => $total,
            'status' => 'pending',
        ]);

        // Create order inside transaction
        DB::beginTransaction();
        try {
            $order = Order::create($orderData);

            foreach ($cartItems as $item) {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                    'subtotal' => $item->product->price * $item->quantity,
                ]);

                // decrement stock
                $item->product->decrement('stock', $item->quantity);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to create order: ' . $e->getMessage());
        }

        // Here you would call the payment gateway. For now, we'll simulate by redirecting to a pay endpoint.
        return redirect()->route('checkout.pay', ['storeSlug' => $store->slug, 'order' => $order->id]);
    }

    public function pay(Request $request, Order $order)
    {
        $storeSlug = $request->route('storeSlug');
        $store = Store::where('slug', $storeSlug)->firstOrFail();

        $customer = Auth::guard('customer')->user();
        if (! $customer || $order->customer_id !== $customer->id || $order->store_id !== $store->id) {
            abort(403);
        }

        // Simulate payment success
        $order->update(['status' => 'paid']);

        // clear customer's cart for this store
        CartItem::where('customer_id', $customer->id)
            ->whereHas('product', fn($q) => $q->where('store_id', $store->id))
            ->delete();

        return redirect()->route('customer.orders.show', ['storeSlug' => $store->slug, 'order' => $order->id])
            ->with('success', 'Payment successful and order placed.');
    }
}
