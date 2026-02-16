<?php

namespace App\Http\Controllers;

use App\Models\MenuCategory;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MenuController extends Controller
{
    public function index(Request $request): Response
{
    $user = auth()->user();
    
    // Get user's favorite menu item IDs
    $favoriteIds = $user ? $user->favoriteMenuItems()->pluck('menu_item_id')->toArray() : [];

    $query = MenuItem::with(['category', 'drinks', 'sauces', 'fries'])
        ->where('is_available', true);

    // Filter by category if provided
    if ($request->has('category') && $request->category !== 'all') {
        $query->where('category_id', $request->category);
    }

    // Search functionality
    if ($request->has('search') && $request->search) {
        $query->where(function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->search . '%')
              ->orWhere('description', 'like', '%' . $request->search . '%');
        });
    }

    // Sort: favorites first, then by name
    $menuItems = $query->get()->map(function ($item) use ($favoriteIds) {
        $item->is_favorited = in_array($item->id, $favoriteIds);
        return $item;
    })->sortByDesc('is_favorited')->values();

    $categories = MenuCategory::orderBy('name')->get();

    return Inertia::render('Menu/Index', [
        'menuItems' => $menuItems,
        'categories' => $categories,
        'filters' => $request->only(['category', 'search']),
    ]);
}

    public function category(string $slug): Response
    {
        $category = MenuCategory::with(['activeItems' => function ($query) {
            $query->ordered();
        }])
            ->where('slug', $slug)
            ->active()
            ->firstOrFail();

        $categories = MenuCategory::active()->ordered()->get();

        return Inertia::render('Menu/Category', [
            'category' => $category,
            'categories' => $categories,
        ]);
    }
}