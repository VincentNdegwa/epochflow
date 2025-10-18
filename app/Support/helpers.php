<?php

use App\Models\Store;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

if (! function_exists('current_store')) {

    function current_store($guard = null): ?Store
    {
        if (isset($guard)) {

            $customer = Auth::guard($guard)->user();
            if ($customer && method_exists($customer, 'store')) {
                return $customer->store;
            }
        } else {

            $user = Auth::user();
            if ($user) {
                if (method_exists($user, 'store')) {
                    return $user->store;
                }
                if (method_exists($user, 'stores')) {
                    return $user->stores()->first();
                }
            }
        }

        return null;
    }
}

if (! function_exists('file_url')) {

    function file_url(?string $path, string $disk = 'public'): ?string
    {
        if (! $path) {
            return null;
        }

        if (preg_match('/^https?:\/\//i', $path)) {
            return $path;
        }

        try {
            return Storage::disk($disk)->url($path);
        } catch (\Exception $e) {
            try {
                return Storage::url($path);
            } catch (\Exception $e) {
                return null;
            }
        }
    }
}
