<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ShopController extends Controller
{
    public function show(Request $request)
    {
        $storeSlug = $request->route('storeSlug');
        $productSlug = $request->route('slug'); // Get slug from route parameter

        $store = Store::where('slug', $storeSlug)->firstOrFail();
        $product = Product::findBySlug($productSlug);
        if (! $product->is_active) {
            abort(404);
        }

        $relatedProducts = Product::with(['category', 'images'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->where('store_id', $store->id)
            ->take(4)
            ->get();

        $cartItemsCount = 0;
        if ($customer = Auth::guard('customer')->user()) {
            $cartItemsCount = CartItem::where('customer_id', $customer->id)->count();
        }

        $template = $store->template ?? 'default';

        return Inertia::render("templates/{$template}/pages/Shop/Product/Show", [
            'store' => $store,
            'product' => $product->load(['category', 'images']),
            'relatedProducts' => $relatedProducts,
            'cartItemsCount' => $cartItemsCount,
            'customer' => $customer ?? null,
        ]);
    }
}
