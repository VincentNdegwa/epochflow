<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;

class StoreController extends Controller
{
    public function index()
    {
        $stores = Store::where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return Inertia::render('Stores/Index', [
            'stores' => $stores,
        ]);
    }

    public function create()
    {
        return Inertia::render('Stores/Form');
    }

    public function show(Request $request)
    {
        $store = Store::where('slug', $request->slug)->firstOrFail();

        $query = $store->products()->where('is_active', true)
            ->with('category');

        if ($request->category) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('id', $request->category);
            });
        }

        if ($request->search) {
            $search = '%'.$request->search.'%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                    ->orWhere('description', 'like', $search);
            });
        }

        switch ($request->get('sort', 'latest')) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(12);

        $newArrivals = $store->products()
            ->with('category')
            ->where('is_active', true)
            ->latest()
            ->take(8)
            ->get();

        $bestSellers = $store->products()
            ->with('category')
            ->where('is_active', true)
            ->orderBy('price', 'desc') // Temporary, ideally would be by sales_count
            ->take(8)
            ->get();

        $featuredCategories = $store->categories()
            ->withCount('products')
            ->orderBy('products_count', 'desc')
            ->where('is_active', true)
            ->get();

        $customer = Auth::guard('customer')->user();

        $cartItemsCount = 0;
        if ($customer) {
            $cartItemsCount = $customer->cartItems()->count();
        }

        $template = $store->template ?? 'default';

        return Inertia::render("templates/{$template}/pages/Shop/Index", [
            'store' => $store,
            'categories' => $store->categories,
            'products' => $products,
            'newArrivals' => $newArrivals,
            'bestSellers' => $bestSellers,
            'featuredCategories' => $featuredCategories,
            'filters' => [
                'category' => $request->category,
                'sort' => $request->get('sort', 'latest'),
                'search' => $request->get('search'),
            ],
            'customer' => $customer,
            'cartItemsCount' => $cartItemsCount,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => [
                'required',
                'string',
                'max:255',
                'unique:stores',
                'regex:/^[a-z0-9-]+$/',
            ],
            'description' => 'nullable|string',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['slug'] = Str::slug(Str::lower($validated['slug']));

        Store::create($validated);

        return redirect()->route('stores.index')
            ->with('success', 'Store created successfully.');
    }

    public function edit(Request $request)
    {
        $store = Store::where('slug', $request->slug)->first();

        \Illuminate\Support\Facades\Gate::authorize('update', $store);

        return Inertia::render('Stores/Form', [
            'store' => $store,
        ]);
    }

    public function update(Request $request)
    {
        $store = Store::where('slug', $request->slug)->first();

        \Illuminate\Support\Facades\Gate::authorize('update', $store);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => [
                'required',
                'string',
                'max:255',
                'unique:stores,slug,'.$store->id,
                'regex:/^[a-z0-9-]+$/',
            ],
            'description' => 'nullable|string',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug(Str::lower($validated['slug']));

        $store->update($validated);

        return redirect()->route('stores.index')
            ->with('success', 'Store updated successfully.');
    }

    public function destroy(Request $request)
    {
        $store = Store::where('slug', $request->slug)->first();
        \Illuminate\Support\Facades\Gate::authorize('delete', $store);

        if ($store->products()->count() > 0) {
            return back()->with('error', 'Cannot delete store with associated products.');
        }

        $store->delete();

        return redirect()->route('stores.index')
            ->with('success', 'Store deleted successfully.');
    }
}
