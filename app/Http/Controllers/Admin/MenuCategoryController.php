<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MenuCategoryController extends Controller
{
    public function index(): Response
    {
        $categories = MenuCategory::withCount('items')
            ->orderBy('sort_order')
            ->get();

        return Inertia::render('Admin/Menu/Categories/Index', [
            'categories' => $categories,
        ]);
    }

    public function create(): Response
    {

        return Inertia::render('Admin/Menu/Categories/Create');
    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'name_en'        => 'nullable|string|max:255',
            'slug'           => 'nullable|string|max:255|unique:menu_categories,slug',
            'description'    => 'nullable|string',
            'description_en' => 'nullable|string',
            'sort_order'     => 'nullable|integer|min:0',
            'is_active'      => 'boolean',
        ]);

        MenuCategory::create($validated);

        return redirect()->route('admin.menu.categories.index')
            ->with('success', 'Category created successfully!');
    }

    public function edit(MenuCategory $category): Response
    {

        return Inertia::render('Admin/Menu/Categories/Edit', [
            'category' => $category,
        ]);
    }

    public function update(Request $request, MenuCategory $category)
    {

        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'name_en'        => 'nullable|string|max:255',
            'slug'           => 'nullable|string|max:255|unique:menu_categories,slug,' . $category->id,
            'description'    => 'nullable|string',
            'description_en' => 'nullable|string',
            'sort_order'     => 'nullable|integer|min:0',
            'is_active'      => 'boolean',
        ]);

        $category->update($validated);

        return redirect()->route('admin.menu.categories.index')
            ->with('success', 'Category updated successfully!');
    }

    public function destroy(MenuCategory $category)
    {

        $category->delete();

        return redirect()->route('admin.menu.categories.index')
            ->with('success', 'Category deleted successfully!');
    }

    public function updateOrder(Request $request)
    {

        $validated = $request->validate([
            'categories' => 'required|array',
            'categories.*.id' => 'required|exists:menu_categories,id',
            'categories.*.sort_order' => 'required|integer|min:0',
        ]);

        foreach ($validated['categories'] as $categoryData) {
            MenuCategory::where('id', $categoryData['id'])
                ->update(['sort_order' => $categoryData['sort_order']]);
        }

        return redirect()->back()->with('success', 'Category order updated!');
    }
}