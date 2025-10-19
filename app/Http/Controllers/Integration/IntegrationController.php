<?php

namespace App\Http\Controllers\Integration;

use App\Http\Controllers\Controller;
use App\Models\PaymentIntegration;
use Illuminate\Http\Request;
use Inertia\Inertia;

class IntegrationController extends Controller
{
    public function toggleIntegration(Request $request, string $provider)
    {
        $store = $request->user()->currentStore;
        
        $integration = PaymentIntegration::where('store_id', $store->id)
            ->where('provider', $provider)
            ->firstOrFail();

        if (!$integration->is_configured) {
            return response()->json([
                'message' => 'Integration must be configured before it can be enabled'
            ], 400);
        }

        $integration->is_enabled = !$integration->is_enabled;
        $integration->save();

        return response()->json([
            'enabled' => $integration->is_enabled
        ]);
    }

    public function index(Request $request)
    {
        $store = current_store();

        $integrations = PaymentIntegration::where('store_id', $store->id)
            ->get()
            ->keyBy('provider');

        $categories = [
            [
                'id' => 'payment',
                'name' => 'Payment',
                'description' => 'Payment gateway integrations',
                'icon' => 'credit-card',
                'integrations' => [
                    [
                        'id' => 'stripe',
                        'name' => 'Stripe',
                        'description' => 'Accept credit card payments with Stripe',
                        'icon' => 'stripe',
                        'enabled' => $integrations->get('stripe')?->is_enabled ?? false,
                        'configured' => $integrations->get('stripe')?->is_configured ?? false,
                        'settings' => [
                            'public_key' => $integrations->get('stripe')?->credentials['public_key'] ?? '',
                            'secret_key' => $integrations->get('stripe')?->credentials['secret_key'] ?? '',
                            'webhook_secret' => $integrations->get('stripe')?->credentials['webhook_secret'] ?? '',
                        ]
                    ],
                    [
                        'id' => 'paypal',
                        'name' => 'PayPal',
                        'description' => 'Accept PayPal payments',
                        'icon' => 'paypal',
                        'enabled' => $integrations->get('paypal')?->is_enabled ?? false,
                        'configured' => $integrations->get('paypal')?->is_configured ?? false,
                        'settings' => [
                            'client_id' => $integrations->get('paypal')?->credentials['client_id'] ?? '',
                            'client_secret' => $integrations->get('paypal')?->credentials['client_secret'] ?? '',
                            'webhook_id' => $integrations->get('paypal')?->credentials['webhook_id'] ?? '',
                            'merchant_id' => $integrations->get('paypal')?->provider_id ?? '',
                        ],
                        'metadata' => $integrations->get('paypal')?->metadata ?? null
                    ],
                ]
            ],
        ];

        return Inertia::render('Integrations/Index', [
            'categories' => $categories
        ]);
    }
}