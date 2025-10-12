<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['items.product', 'user'])
            ->where('user_id', auth()->id());

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $orders = $query->latest()
            ->paginate(10);

        return Inertia::render('Orders/Index', [
            'orders' => $orders,
            'filters' => [
                'status' => $request->status ?? ''
            ]
        ]);
    }

    public function show(Order $order)
    {
        \Illuminate\Support\Facades\Gate::authorize('view', $order);

        return Inertia::render('Orders/Show', [
            'order' => $order->load('items.product')
        ]);
    }

    public function store(Request $request)
    {
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
            'notes' => 'nullable|string'
        ]);

        $cartItems = CartItem::with('product')
            ->where('user_id', auth()->id())
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->back()
                ->with('error', 'Your cart is empty.');
        }

        $totalAmount = $cartItems->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });

        $validated['user_id'] = auth()->id();
        $validated['order_number'] = 'ORD-' . Str::random(10);
        $validated['total_amount'] = $totalAmount;
        $validated['status'] = 'pending';

        $order = Order::create($validated);

        foreach ($cartItems as $item) {
            $order->items()->create([
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->product->price,
                'subtotal' => $item->product->price * $item->quantity
            ]);

            // Decrease product stock
            $item->product->decrement('stock', $item->quantity);
        }

        // Clear cart
        CartItem::where('user_id', auth()->id())->delete();

        return redirect()->route('orders.show', $order)
            ->with('success', 'Order placed successfully.');
    }

    public function cancel(Order $order)
    {
        \Illuminate\Support\Facades\Gate::authorize('update', $order);

        if ($order->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'This order cannot be cancelled.');
        }

        $order->update(['status' => 'cancelled']);

        // Restore product stock
        foreach ($order->items as $item) {
            $item->product->increment('stock', $item->quantity);
        }

        return redirect()->back()
            ->with('success', 'Order cancelled successfully.');
    }
}
