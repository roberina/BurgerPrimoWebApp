<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use Illuminate\Http\Request;

class MenuFavoriteController extends Controller
{
    /**
     * Toggle favorite status for a menu item
     */
    public function toggle(MenuItem $menuItem)
    {
        $user = auth()->user();

        if ($user->favoriteMenuItems()->where('menu_item_id', $menuItem->id)->exists()) {
            // Remove from favorites
            $user->favoriteMenuItems()->detach($menuItem->id);
            $message = 'Eemaldatud lemmikutest';
        } else {
            // Add to favorites
            $user->favoriteMenuItems()->attach($menuItem->id);
            $message = 'Lisatud lemmikutesse';
        }

        return redirect()->back()->with('success', $message);
    }
}
