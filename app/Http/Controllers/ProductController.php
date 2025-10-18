<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index()
    {
        $store = current_store();
        if (! $store) {
            return redirect()->back()
                ->with('error', 'No store context found. Please ensure you are logged in and associated with a store.');
        }

        $products = Product::where('store_id', $store->id)->with(['category', 'images'])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(12);

        return Inertia::render('Products/Index', [
            'products' => $products,
        ]);
    }

    public function create()
    {
        $categories = Category::all();

        return Inertia::render('Products/Form', [
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $store = current_store();
        if (! $store) {
            return redirect()->back()
                ->with('error', 'No store context found. Please ensure you are logged in and associated with a store.');
        }
        $validated['store_id'] = $store->id;

        $validated['user_id'] = auth()->id();
        $validated['slug'] = Str::slug($validated['name']);

        $product = Product::create($validated);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                $product->images()->create([
                    'path' => $path,
                    'is_primary' => $product->images()->count() === 0,
                ]);
            }
        }

        return redirect()->route('products.index')
            ->with('success', 'Product created successfully.');
    }

    public function edit(Request $request)
    {
        $product = Product::findBySlug($request->slug);

        \Illuminate\Support\Facades\Gate::authorize('update', $product);

        $categories = Category::all();

        return Inertia::render('Products/Form', [
            'product' => $product->load('images'),
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, Product $product)
    {
        \Illuminate\Support\Facades\Gate::authorize('update', $product);

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $product->update($validated);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                $product->images()->create([
                    'path' => $path,
                    'is_primary' => $product->images()->count() === 0,
                ]);
            }
        }

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        \Illuminate\Support\Facades\Gate::authorize('delete', $product);

        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }
}
