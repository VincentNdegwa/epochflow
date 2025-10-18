<?php

namespace App\Http\Controllers\Integration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class IntegrationController extends Controller
{
    public function index()
    {
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
                        'enabled' => false,
                        'configured' => false,
                        'settings' => [
                            'public_key' => '',
                            'secret_key' => '',
                            'webhook_secret' => '',
                        ]
                    ],
                    [
                        'id' => 'paypal',
                        'name' => 'PayPal',
                        'description' => 'Accept PayPal payments',
                        'icon' => 'paypal',
                        'enabled' => false,
                        'configured' => false,
                        'settings' => [
                            'client_id' => '',
                            'client_secret' => '',
                            'webhook_id' => '',
                        ]
                    ],
                ]
            ],
            // You can add more categories here as needed
        ];

        return Inertia::render('Integrations/Index', [
            'categories' => $categories
        ]);
    }
}