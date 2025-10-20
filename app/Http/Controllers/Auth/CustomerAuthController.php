<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;

class CustomerAuthController extends Controller
{
    public function showLogin(Request $request, $storeSlug)
    {
        $store = Store::where('slug', $storeSlug)->firstOrFail();

        return Inertia::render('Shop/Auth/Login', [
            'store' => $store,
        ]);
    }

    public function showRegister(Request $request, $storeSlug)
    {
        $store = Store::where('slug', $storeSlug)->firstOrFail();

        return Inertia::render('Shop/Auth/Register', [
            'store' => $store,
        ]);
    }

    public function login(Request $request, $storeSlug)
    {
        $store = Store::where('slug', $storeSlug)->firstOrFail();

        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $customer = Customer::where('email', $credentials['email'])
            ->where('store_id', $store->id)
            ->first();

        if (! $customer) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ]);
        }

        if (Auth::guard('customer')->attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
            'store_id' => $store->id,
        ])) {
            $request->session()->regenerate();

            return redirect()->route('stores.show', ['slug' => $storeSlug]);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function register(Request $request, $storeSlug)
    {
        $store = Store::where('slug', $storeSlug)->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                function ($attribute, $value, $fail) use ($store) {
                    $exists = Customer::where('email', $value)
                        ->where('store_id', $store->id)
                        ->exists();
                    if ($exists) {
                        $fail('The email has already been taken in this store.');
                    }
                },
            ],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        $customer = Customer::create([
            'store_id' => $store->id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        Auth::guard('customer')->login($customer);

        return redirect()->route('stores.show', ['slug' => $storeSlug]);
    }

    public function logout(Request $request, $storeSlug)
    {
        Auth::guard('customer')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route("customer.login", ['storeSlug' => $storeSlug]);
    }
}
