<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
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
            'stores' => $stores
        ]);
    }

    public function create()
    {
        return Inertia::render('Stores/Form');
    }

    public function show(Request $request)
    {
        $store = Store::where('slug', $request->slug)->firstOrFail();

        $query = $store->products()->with('categories');

        if ($request->category) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('slug', $request->category);
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

        $cartItemsCount = 0;
        // if (Auth::check()) {
        //     $cartItemsCount = Auth::user()->cartItems()->count();
        // }

        return Inertia::render('Shop/Index', [
            'store' => $store,
            'categories' => $store->categories,
            'products' => $products,
            'filters' => [
                'category' => $request->category,
                'sort' => $request->get('sort', 'latest')
            ],
            'user' => Auth::user(),
            'cartItemsCount' => $cartItemsCount
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
                'regex:/^[a-z0-9-]+$/'
            ],
            'description' => 'nullable|string',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'is_active' => 'boolean'
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
            'store' => $store
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
                'unique:stores,slug,' . $store->id,
                'regex:/^[a-z0-9-]+$/'
            ],
            'description' => 'nullable|string',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'is_active' => 'boolean'
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
