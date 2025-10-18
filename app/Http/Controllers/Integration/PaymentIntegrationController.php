<?php

namespace App\Http\Controllers\Integration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaymentIntegrationController extends Controller
{


    public function configure(Request $request, string $provider)
    {
        switch ($provider) {
            case 'paypal':
                return redirect()->route('integrations.paypal.onboarding');
            
            default:
                # code...
                break;
        }
    }

}