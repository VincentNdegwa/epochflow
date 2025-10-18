<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index()
    {
        $store = current_store();
        if (! $store) {
            return redirect()->back()
                ->with('error', 'No store context found. Please ensure you are logged in and associated with a store.');
        }

        $categories = Category::where('store_id', $store->id)->withCount('products')
            ->latest()
            ->paginate(10);

        return Inertia::render('Categories/Index', [
            'categories' => $categories,
        ]);
    }

    public function create()
    {
        return Inertia::render('Categories/Form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $store = current_store();
        if (! $store) {
            return redirect()->back()
                ->with('error', 'No store context found. Please ensure you are logged in and associated with a store.');
        }

        $validated['store_id'] = $store->id;

        Category::create($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function edit(Request $request)
    {
        $category = Category::findBySlug($request->slug);

        return Inertia::render('Categories/Form', [
            'category' => $category,
        ]);
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,'.$category->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        if ($category->products()->exists()) {
            return redirect()->back()
                ->with('error', 'Cannot delete category with associated products.');
        }

        $category->delete();

        return redirect()->back()
            ->with('success', 'Category deleted successfully.');
    }
}
