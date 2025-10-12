<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ShopController extends Controller
{

    public function show(Product $product)
    {
        if (!$product->is_active) {
            abort(404);
        }

        $relatedProducts = Product::with(['category', 'images'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->take(4)
            ->get();

        $cartItemsCount = 0;
        if (auth()->check()) {
            $cartItemsCount = CartItem::where('user_id', auth()->id())->count();
        }

        return Inertia::render('Shop/Product/Show', [
            'product' => $product->load(['category', 'images']),
            'relatedProducts' => $relatedProducts,
            'cartItemsCount' => $cartItemsCount
        ]);
    }
}
