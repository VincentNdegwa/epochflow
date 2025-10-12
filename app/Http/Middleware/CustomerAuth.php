<?php

namespace App\Http\Middleware;

use App\Models\Store;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CustomerAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $store = Store::where('slug', $request->route('storeSlug'))->first();

        if (!$store) {
            abort(404);
        }

        // Check if customer is logged in and belongs to the current store
        if (
            !Auth::guard('customer')->check() ||
            Auth::guard('customer')->user()->store_id !== $store->id
        ) {
            return redirect()->route('customer.login', ['storeSlug' => $store->slug]);
        }

        return $next($request);
    }
}
