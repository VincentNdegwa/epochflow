<?php

namespace App\Services\PayPal;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Exception;
use Illuminate\Support\Facades\Log;

class PayPalService
{
    private string $baseUrl;
    private string $clientId;
    private string $clientSecret;
    private string $accessToken;

    public function __construct()
    {
        $this->baseUrl = config('services.paypal.sandbox')
            ? 'https://api-m.sandbox.paypal.com'
            : 'https://api-m.paypal.com';
        
        $this->clientId = config('services.paypal.client_id');
        $this->clientSecret = config('services.paypal.client_secret');
    }


    public function getAccessToken(): string
    {
        if ($cachedToken = Cache::get('paypal_access_token')) {
            return $cachedToken;
        }

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Accept-Language' => 'en_US',
            ])
            ->withBasicAuth($this->clientId, $this->clientSecret)
            ->asForm()
            ->post($this->baseUrl . '/v1/oauth2/token', [
                'grant_type' => 'client_credentials'
            ]);

            if (!$response->successful()) {
                throw new Exception('Failed to get PayPal access token: ' . $response->body());
            }

            $data = $response->json();
            $accessToken = $data['access_token'];
            
            $expiresIn = $data['expires_in'] ?? 32400;
            Cache::put('paypal_access_token', $accessToken, now()->addSeconds($expiresIn - 60));

            return $accessToken;
        } catch (Exception $e) {
            throw new Exception('PayPal Authentication Failed: ' . $e->getMessage());
        }
    }

    public function createPartnerReferral(array $referralData): string
    {
        Log::info('Creating PayPal Partner Referral with data: ' . json_encode($referralData));

        $client = $this->getAuthenticatedClient()
            ->withHeaders([
                'PayPal-Partner-Attribution-Id' => config('services.paypal.bn_code'),
            ]);

        $response = $client->post($this->baseUrl . '/v2/customer/partner-referrals', $referralData);

        Log::info('PayPal Partner Referral API response: ' . $response->body());
        
        if (!$response->successful() || !isset($response->json()['links'])) {
             throw new Exception('Partner Referral API failed: ' . $response->body());
        }

        $responseBody = $response->json();

        foreach ($responseBody['links'] as $link) {
            if ($link['rel'] === 'action_url') {
                return $link['href'];
            }
        }

        throw new Exception('Partner Referral successful, but action_url not found in response.');
    }

 
    protected function getAuthenticatedClient()
    {
        return Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->getAccessToken(),
            'Content-Type' => 'application/json',
        ]);
    }

    public function createOrder(array $orderData, string $storeSlug, string $merchantId): array
    {
        try {
            $requestId = uniqid('order_', true);
            $client = $this->getAuthenticatedClient()
                ->withHeaders([
                    'PayPal-Partner-Attribution-Id' => config('services.paypal.bn_code'),
                    'PayPal-Request-Id' => $requestId,
                    'Prefer' => 'return=representation'
                ]);

            $payload = [
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    [
                        'reference_id' => $orderData['reference_id'] ?? $requestId,
                        'amount' => [
                            'currency_code' => $orderData['currency'] ?? 'USD',
                            'value' => number_format($orderData['amount'], 2, '.', ''),
                            'breakdown' => [
                                'item_total' => [
                                    'currency_code' => $orderData['currency'] ?? 'USD',
                                    'value' => number_format($orderData['amount'], 2, '.', '')
                                ]
                            ]
                        ],
                        'payee' => [
                            'merchant_id' => $merchantId
                        ],
                        'description' => $orderData['description'] ?? 'Order payment',
                        'custom_id' => $orderData['custom_id'] ?? null,
                        'invoice_id' => $orderData['invoice_id'] ?? null
                    ]
                ],
                'application_context' => [
                    'return_url' => route('payments.paypal.capture', ['storeSlug'=>$storeSlug]),
                    'cancel_url' => route('payments.paypal.cancel', ['storeSlug'=>$storeSlug]),
                    'brand_name' => $orderData['store_name'] ?? config('app.name'),
                    'landing_page' => 'LOGIN',
                    'shipping_preference' => 'NO_SHIPPING',
                    'user_action' => 'PAY_NOW'
                ]
            ];

            $response = $client->post($this->baseUrl . '/v2/checkout/orders', $payload);

            if (!$response->successful()) {
                Log::error('PayPal Create Order Error: ' . $response->body());
                throw new Exception('Failed to create PayPal order: ' . $response->json()['message'] ?? 'Unknown error');
            }

            $orderResponse = $response->json();

            Log::info('Order data: ' . json_encode($orderData));

            $confirmPayload = [
                'payment_source' => [
                    'paypal' => [
                        'name' => [
                            'given_name' => $orderData['customer_name'] ?? '',
                            'surname' => ''
                        ],
                        'email_address' => $orderData['customer_email'] ?? '',
                        'experience_context' => [
                            'payment_method_preference' => 'IMMEDIATE_PAYMENT_REQUIRED',
                            'brand_name' => $orderData['store_name'] ?? config('app.name'),
                            'locale' => 'en-US',
                            'landing_page' => 'LOGIN',
                            'shipping_preference' => 'NO_SHIPPING',
                            'user_action' => 'PAY_NOW',
                            'return_url' => route('payments.paypal.capture', ['storeSlug'=>$storeSlug]),
                            'cancel_url' => route('payments.paypal.cancel', ['storeSlug'=>$storeSlug])
                        ]
                    ]
                ]
            ];

            $confirmResponse = $client->post($this->baseUrl . "/v2/checkout/orders/{$orderResponse['id']}/confirm-payment-source", $confirmPayload);

            if (!$confirmResponse->successful()) {
                Log::error('PayPal Confirm Payment Source Error: ' . $confirmResponse->body());
                Log::info('Proceeding with order despite confirmation failure');
            }

            return $orderResponse;
        } catch (Exception $e) {
            Log::error('PayPal Create Order Exception: ' . $e->getMessage());
            throw new Exception('Failed to create PayPal order: ' . $e->getMessage());
        }
    }


    public function captureOrder(string $orderId): array
    {
        try {
            // Get the order details first
            $orderDetails = $this->getOrder($orderId);
            
            if ($orderDetails['status'] === 'COMPLETED') {
                return $orderDetails;
            }


            $client = $this->getAuthenticatedClient()
                ->withHeaders([
                    'PayPal-Partner-Attribution-Id' => config('services.paypal.bn_code'),
                    'PayPal-Request-Id' => uniqid('capture_', true),
                    'Prefer' => 'return=representation'
                ]);

            Log::info('Capturing PayPal order with ID: ' . $orderId);

            $response = $client->post($this->baseUrl . "/v2/checkout/orders/{$orderId}/capture", (object) []);

            if (!$response->successful()) {
                Log::error('PayPal Capture Order Error: ' . $response->body());
                throw new Exception('Failed to capture PayPal payment: ' . $response->json()['message'] ?? 'Unknown error');
            }

            return $response->json();
        } catch (Exception $e) {
            Log::error('PayPal Capture Order Exception: ' . $e->getMessage());
            throw new Exception('Failed to capture PayPal payment: ' . $e->getMessage());
        }
    }

    public function getOrder(string $orderId): array
    {
        try {
            $client = $this->getAuthenticatedClient();
            $response = $client->get($this->baseUrl . "/v2/checkout/orders/{$orderId}");

            if (!$response->successful()) {
                Log::error('PayPal Get Order Error: ' . $response->body());
                throw new Exception('Failed to get PayPal order details: ' . $response->json()['message'] ?? 'Unknown error');
            }

            return $response->json();
        } catch (Exception $e) {
            Log::error('PayPal Get Order Exception: ' . $e->getMessage());
            throw new Exception('Failed to get PayPal order details: ' . $e->getMessage());
        }
    }
}