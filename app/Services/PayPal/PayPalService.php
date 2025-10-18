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
}