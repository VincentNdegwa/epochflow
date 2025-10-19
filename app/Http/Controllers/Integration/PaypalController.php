<?php

namespace App\Http\Controllers\Integration;

use App\Http\Controllers\Controller;
use App\Models\PaymentIntegration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class PaypalController extends Controller
{
    public function onboarding(Request $request)
    {

        $payPalService = app()->make(\App\Services\PayPal\PayPalService::class);

        $sellerTrackingId = 'UNIQUE_ID_' . time();
        // $yourReturnUrl = route('integrations.paypal.onboarding.return');
        $yourReturnUrl = 'https://b7b623785e91.ngrok-free.app/integrations/paypal/return';

        $referralData = [
            'tracking_id' => $sellerTrackingId,
            'operations' => [
                [
                    'operation' => 'API_INTEGRATION',
                    'api_integration_preference' => [
                        'rest_api_integration' => [
                            'integration_method' => 'PAYPAL',
                            'integration_type' => 'THIRD_PARTY',
                            'third_party_details' => [
                                'features' => [
                                    'PAYMENT',
                                    'REFUND',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'products' => [
                'PPCP',
            ],
            'legal_consents' => [
                [
                    'type' => 'SHARE_DATA_CONSENT',
                    'granted' => true,
                ],
            ],
            'partner_config_override' => [
                'return_url' => $yourReturnUrl,
            ],
        ];

        try {
            $actionUrl = $payPalService->createPartnerReferral($referralData);
            Log::info('PayPal Partner Referral created successfully: ' . $actionUrl);
            
            return response()->json([
                'url' => $actionUrl . '&displayMode=minibrowser'
            ]);
        } catch (\Exception $e) {
            Log::error('PayPal onboarding error: ' . $e->getMessage());
            if ($request->wantsJson()) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Failed to initiate PayPal onboarding: ' . $e->getMessage());
        }
    }


    public function handleReturn(Request $request)
    {
        Log::info('PayPal return callback received', $request->all());

        $trackingId = $request->query('merchantId');
        $paypalMerchantId = $request->query('merchantIdInPayPal');
        $permissionsGranted = $request->query('permissionsGranted');
        $accountStatus = $request->query('accountStatus');
        $isEmailConfirmed = $request->query('isEmailConfirmed');
        $riskStatus = $request->query('riskStatus');

        try {
            if ($permissionsGranted === 'true' && $paypalMerchantId) {
                $store = current_store();

                $integration = PaymentIntegration::updateOrCreate(
                    [
                        'store_id' => $store->id,
                        'provider' => 'paypal'
                    ],
                    [
                        'provider_id' => $paypalMerchantId,
                        'status' => 'active',
                        'is_configured' => true,
                        'is_enabled' => true,
                        'metadata' => [
                            'tracking_id' => $trackingId,
                            'account_status' => $accountStatus,
                            'email_confirmed' => $isEmailConfirmed === 'true',
                            'risk_status' => $riskStatus,
                            'permissions_granted' => true
                        ]
                    ]
                );

                Log::info('PayPal integration saved successfully', ['integration_id' => $integration->id]);
                
                return redirect()->route('integrations.index')->with('success', 'PayPal account connected successfully!');
            }

            return redirect()->route('integrations.index')->with('error', 'PayPal connection was not completed.');
        } catch (\Exception $e) {
            Log::error('PayPal return handler error: ' . $e->getMessage());
            return redirect()->route('integrations.index')->with('error', 'Failed to complete PayPal connection.');
        }
    }
}
