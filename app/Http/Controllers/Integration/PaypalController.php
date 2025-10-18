<?php

namespace App\Http\Controllers\Integration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class PaypalController extends Controller
{
    public function onboarding(Request $request)
    {

        $payPalService = app()->make(\App\Services\PayPal\PayPalService::class);

        $sellerTrackingId = 'UNIQUE_ID_' . time();
        $yourReturnUrl = route('integrations.paypal.onboarding.return');
        // $yourReturnUrl = 'https://11b46720b009.ngrok-free.app/integrations/paypal/return';

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
            
            if ($request->wantsJson()) {
                return response()->json([
                    'url' => $actionUrl . '&displayMode=minibrowser'
                ]);
            }
            
            return Inertia::render('Integrations/PayPal/Onboarding', [
                'paypalUrl' => $actionUrl . '&displayMode=minibrowser'
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

        $merchantId = $request->query('merchantId');
        $paypalMerchantId = $request->query('merchantIdInPayPal');
        $permissionsGranted = $request->query('permissionsGranted');

        try {
            if ($permissionsGranted === 'true' && $paypalMerchantId) {
                // Here you would typically:
                // 1. Save the PayPal merchant info to your database
                // 2. Update the integration status
                // 3. Generate and store any necessary tokens
                
                // For now, we'll just redirect with a success message
                return redirect()->route('integrations.index')->with('success', 'PayPal account connected successfully!');
            }

            return redirect()->route('integrations.index')->with('error', 'PayPal connection was not completed.');
        } catch (\Exception $e) {
            Log::error('PayPal return handler error: ' . $e->getMessage());
            return redirect()->route('integrations.index')->with('error', 'Failed to complete PayPal connection.');
        }
    }
}
