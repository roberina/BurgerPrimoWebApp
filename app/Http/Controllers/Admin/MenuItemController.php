<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\MenuCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class MenuItemController extends Controller
{
    public function index(Request $request): Response
    {
        // Flat list of all categories for the filter dropdown
        $allCategories = MenuCategory::orderBy('sort_order')->get(['id', 'name']);

        // Build the item query with optional filters
        $itemQuery = MenuItem::with('category')->orderBy('sort_order');

        if ($request->filled('category_id')) {
            $itemQuery->where('category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $itemQuery->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $items = $itemQuery->get();

        // Group items by category for the new grouped layout
        // If a category filter is active, only show the matching category group
        $categoryQuery = MenuCategory::orderBy('sort_order');

        if ($request->filled('category_id')) {
            $categoryQuery->where('id', $request->category_id);
        }

        $categories = $categoryQuery->get()->map(function ($category) use ($items) {
            return [
                'id'        => $category->id,
                'name'      => $category->name,
                'is_active' => (bool) $category->is_active,
                'items'     => $items
                    ->where('category_id', $category->id)
                    ->values()
                    ->map(fn ($item) => [
                        'id'                  => $item->id,
                        'name'                => $item->name,
                        'description'         => $item->description,
                        'price'               => (float) $item->price,
                        'original_price'      => $item->original_price ? (float) $item->original_price : null,
                        'image_url'           => $item->image_url,
                        'is_featured'         => (bool) $item->is_featured,
                        'is_active'           => (bool) $item->is_active,
                        'discount_percentage' => $item->discount_percentage,
                        'category'            => ['id' => $item->category->id, 'name' => $item->category->name],
                    ]),
            ];
        });

        return Inertia::render('Admin/Menu/Items/Index', [
            'categories'     => $categories,
            'all_categories' => $allCategories,
            'filters'        => $request->only(['category_id', 'search']),
        ]);
    }

    public function create(): Response
    {
        $categories = MenuCategory::orderBy('sort_order')->get();

        return Inertia::render('Admin/Menu/Items/Create', [
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id'         => 'required|exists:menu_categories,id',
            'name'                => 'required|string|max:255',
            'description'         => 'required|string',
            'price'               => 'required|numeric|min:0',
            'original_price'      => 'nullable|numeric|min:0',
            'image'               => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'is_featured'         => 'boolean',
            'is_active'           => 'boolean',
            'sort_order'          => 'nullable|integer|min:0',
            'size'                => 'nullable|string|max:100',
            'discount_percentage' => 'nullable|integer|min:0|max:100',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('menu-items', 'public');
        }

        MenuItem::create($validated);

        return redirect()->route('admin.menu.items.index')
            ->with('success', 'Toode edukalt lisatud!');
    }

    public function edit(MenuItem $item): Response
    {
        $categories = MenuCategory::orderBy('sort_order')->get();

        return Inertia::render('Admin/Menu/Items/Edit', [
            'item'       => $item,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, MenuItem $item)
    {
        $validated = $request->validate([
            'category_id'         => 'required|exists:menu_categories,id',
            'name'                => 'required|string|max:255',
            'description'         => 'required|string',
            'price'               => 'required|numeric|min:0',
            'original_price'      => 'nullable|numeric|min:0',
            'image'               => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'is_featured'         => 'boolean',
            'is_active'           => 'boolean',
            'sort_order'          => 'nullable|integer|min:0',
            'size'                => 'nullable|string|max:100',
            'discount_percentage' => 'nullable|integer|min:0|max:100',
        ]);

        if ($request->hasFile('image')) {
            if ($item->image) {
                Storage::disk('public')->delete($item->image);
            }
            $validated['image'] = $request->file('image')->store('menu-items', 'public');
        }

        $item->update($validated);

        return redirect()->route('admin.menu.items.index')
            ->with('success', 'Toode edukalt uuendatud!');
    }

    public function destroy(MenuItem $item)
    {
        if ($item->image) {
            Storage::disk('public')->delete($item->image);
        }

        $item->delete();

        return redirect()->route('admin.menu.items.index')
            ->with('success', 'Toode edukalt kustutatud!');
    }

    public function updateOrder(Request $request)
    {
        $validated = $request->validate([
            'items'              => 'required|array',
            'items.*.id'         => 'required|exists:menu_items,id',
            'items.*.sort_order' => 'required|integer|min:0',
        ]);

        foreach ($validated['items'] as $itemData) {
            MenuItem::where('id', $itemData['id'])
                ->update(['sort_order' => $itemData['sort_order']]);
        }

        return redirect()->back()->with('success', 'Järjekord uuendatud!');
    }
}