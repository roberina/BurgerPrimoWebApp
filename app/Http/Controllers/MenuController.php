<?php

namespace App\Http\Controllers;

use App\Models\MenuCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MenuController extends Controller
{
    public function index(Request $request): Response
{
    $user = auth()->user();
    
    // Get user's favorite menu item IDs
    $favoriteMenuItemIds = $user ? $user->favoriteMenuItems()->pluck('menu_item_id')->toArray() : [];

    // Get categories with menu items
    $categories = MenuCategory::with(['menuItems' => function($query) {
        $query->where('is_available', true)
              ->where('is_active', true)
              ->orderBy('sort_order');
    }])
    ->orderBy('sort_order')
    ->get()
    ->map(function ($category) use ($favoriteMenuItemIds) {
        $category->active_items = $category->menuItems->map(function ($item) use ($favoriteMenuItemIds) {
            $item->is_favorited = in_array($item->id, $favoriteMenuItemIds);
            return $item;
        });
        return $category;
    });

    // Get favorite menu items
    $favoriteMenuItems = $user ? $user->favoriteMenuItems()
        ->where('is_available', true)
        ->where('is_active', true)
        ->with('category')
        ->get()
        ->map(function ($item) {
            $item->type = 'menu_item';
            $item->is_favorited = true;
            return $item;
        }) : collect();

    // Get favorite custom burgers
    $favoriteCustomBurgers = $user ? $user->customBurgers()
        ->with(['ingredients' => function($query) {
            $query->select('ingredients.id', 'ingredients.name', 'ingredients.category', 'ingredients.price', 'ingredients.is_available');
        }])
        ->where('is_favorite', true)
        ->get()
        ->map(function ($burger) {
            $burger->type = 'custom_burger';
            return $burger;
        }) : collect();

    // Combine favorites
    $allFavorites = $favoriteMenuItems->merge($favoriteCustomBurgers);

    return Inertia::render('Menu/Index', [
        'categories' => $categories,
        'favorites' => $allFavorites,
    ]);
}
}