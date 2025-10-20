<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PaymentIntegration;
use App\Services\PayPal\PayPalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PayPalPaymentController extends Controller
{
    protected PayPalService $paypalService;

    public function __construct(PayPalService $paypalService)
    {
        $this->paypalService = $paypalService;
    }

    public function createOrder(Request $request, $store_slug ,$order_id)
    {
        try {
            $order = Order::findOrFail($order_id);
            
            // Update payment provider before creating PayPal order
            $order->update([
                'payment_provider' => 'paypal',
                'payment_status' => 'PENDING'
            ]);

            $order->load('customer');
            
            $paypalIntegration = PaymentIntegration::where('store_id', $order->store_id)
                ->where('provider', 'paypal')
                ->where('is_configured', true)
                ->where('is_enabled', true)
                ->firstOrFail();

            $orderData = [
                'reference_id' => $order->id,
                'amount' => $order->total_amount,
                'currency' => $order->currency ?? 'USD',
                'description' => "Payment for Order #{$order->id}",
                'custom_id' => $order->id,
                'store_name' => $order->store->name,
                'customer_name' => $order->customer->name,
                'customer_email' => $order->customer->email,
            ];

            $paypalOrder = $this->paypalService->createOrder($orderData, $store_slug, $paypalIntegration->provider_id);

            $order->update([
                'payment_provider' => 'paypal',
                'payment_id' => $paypalOrder['id'],
                'payment_status' => $paypalOrder['status']
            ]);

            // Return the PayPal order ID and approval URL
            $approvalLink = collect($paypalOrder['links'])
                ->firstWhere('rel', 'approve')['href'];

            return response()->json([
                'order_id' => $paypalOrder['id'],
                'approval_url' => $approvalLink
            ]);
        } catch (\Exception $e) {
            Log::error('PayPal Create Order Error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to create PayPal order',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function capture(Request $request)
    {
        try {
            $token = $request->input('token');
            
            $order = Order::where('payment_id', $token)->first();
            if (!$order) {
                $order = Order::where('payment_provider', 'paypal')
                    ->where('status', 'pending')
                    ->orderBy('created_at', 'desc')
                    ->first();
                
                if (!$order) {
                    throw new \Exception('Order not found');
                }
                
                $order->update(['payment_id' => $token]);
            }
            
            $paypalOrder = $this->paypalService->captureOrder($token);

            $paymentStatus = $paypalOrder['status'];
            $order->update([
                'payment_status' => $paymentStatus,
                'payment_completed_at' => now(),
                'status' => $paymentStatus === 'COMPLETED' ? 'paid' : 'payment_failed'
            ]);

            if ($paymentStatus === 'COMPLETED') {
                return redirect()->route('customer.orders.show', [
                    'storeSlug' => $order->store->slug,
                    'slug' => $order->slug
                ])->with('success', 'Payment successful! Your order has been confirmed.');
            }

            return redirect()->route('customer.orders.show', [
                'storeSlug' => $order->store->slug,
                'slug' => $order->slug
            ])->with('error', 'Payment failed. Please try again or contact support.');
        } catch (\Exception $e) {
            Log::error('PayPal Capture Error: ' . $e->getMessage());
            return redirect()->route('customer.orders.show', [
                'storeSlug' => $order->store->slug,
                'slug' => $order->slug
            ])->with('error', 'Payment processing failed. Please try again or contact support.');
        }
    }

    public function cancel(Request $request)
    {
        $orderId = $request->input('token');
        $order = Order::where('payment_id', $orderId)->first();

        if ($order) {
            $order->update([
                'payment_status' => 'CANCELLED',
                'status' => 'cancelled'
            ]);

            return redirect()->route('customer.orders.show', [
                'storeSlug' => $order->store->slug,
                'slug' => $order->slug
            ])->with('info', 'Payment was cancelled.');
        }

        return redirect()->back()->with('info', 'Payment was cancelled.');
    }
}