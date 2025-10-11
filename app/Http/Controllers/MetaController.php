<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class MetaController extends Controller
{
    public function index()
    {
        return Socialite::driver('facebook')
            ->setScopes(['catalog_management'])
            ->redirect();
    }

    public function callback(Request $request)
    {

        $driver = Socialite::driver('facebook');

        if (config('services.socialite_stateless', false)) {
            $driver = $driver->stateless();
        }

        $user = $driver->user();

        return response()->json($user);
    }
}
