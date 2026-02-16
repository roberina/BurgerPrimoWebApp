<?php

namespace App\Http\Controllers;

use App\Models\CustomBurger;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class BurgerBuilderController extends Controller
{
    use AuthorizesRequests;
    const MAX_BURGERS_PER_USER = 3;

    public function index(): Response
    {
        $ingredients = Ingredient::where('is_available', true)
            ->orderBy('category')
            ->orderBy('name')
            ->get()
            ->groupBy('category');

        // Get custom burgers with availability check
        $customBurgers = auth()->user()
            ->customBurgers()
            ->with(['ingredients' => function($query) {
                $query->select('ingredients.id', 'ingredients.name', 'ingredients.category', 'ingredients.price', 'ingredients.is_available');
            }])
            ->orderByDesc('is_favorite')
            ->orderByDesc('created_at')
            ->get();

        // Check if user has reached the limit
        $canCreateMore = $customBurgers->count() < self::MAX_BURGERS_PER_USER;

        return Inertia::render('BurgerBuilder/Index', [
            'ingredients' => $ingredients,
            'customBurgers' => $customBurgers,
            'canCreateMore' => $canCreateMore,
            'maxBurgers' => self::MAX_BURGERS_PER_USER,
        ]);
    }

    public function store(Request $request)
    {
        // Check burger limit
        $userBurgersCount = auth()->user()->customBurgers()->count();
        
        if ($userBurgersCount >= self::MAX_BURGERS_PER_USER) {
            return redirect()->back()->with('error', 'Oled jõudnud maksimaalse burgeri limiidini (' . self::MAX_BURGERS_PER_USER . '). Kustuta mõni olemasolev burger, et luua uus.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'ingredients' => 'required|array|min:1',
            'ingredients.*.id' => 'required|exists:ingredients,id',
            'ingredients.*.quantity' => 'required|integer|min:1|max:10',
            'is_favorite' => 'boolean',
        ]);

        $burger = CustomBurger::create([
            'user_id' => auth()->id(),
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'total_price' => 0,
            'is_favorite' => $validated['is_favorite'] ?? false,
        ]);

        // Attach ingredients with quantities
        foreach ($validated['ingredients'] as $ingredientData) {
            $burger->ingredients()->attach($ingredientData['id'], [
                'quantity' => $ingredientData['quantity'],
            ]);
        }

        // Calculate and update total price
        $totalPrice = $burger->calculateTotalPrice();
        $burger->update(['total_price' => $totalPrice]);

        return redirect()->back()->with('success', 'Burger loodud edukalt!');
    }

    public function update(Request $request, CustomBurger $burger)
    {
        $this->authorize('update', $burger);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'ingredients' => 'required|array|min:1',
            'ingredients.*.id' => 'required|exists:ingredients,id',
            'ingredients.*.quantity' => 'required|integer|min:1|max:10',
            'is_favorite' => 'boolean',
        ]);

        $burger->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_favorite' => $validated['is_favorite'] ?? false,
        ]);

        // Sync ingredients
        $syncData = [];
        foreach ($validated['ingredients'] as $ingredientData) {
            $syncData[$ingredientData['id']] = ['quantity' => $ingredientData['quantity']];
        }
        $burger->ingredients()->sync($syncData);

        // Recalculate price
        $totalPrice = $burger->calculateTotalPrice();
        $burger->update(['total_price' => $totalPrice]);

        return redirect()->back()->with('success', 'Burger uuendatud edukalt!');
    }

    public function destroy(CustomBurger $burger)
    {
        $this->authorize('delete', $burger);

        $burger->delete();

        return redirect()->back()->with('success', 'Burger kustutatud edukalt!');
    }

    public function toggleFavorite(CustomBurger $burger)
    {
        $this->authorize('update', $burger);

        $burger->update([
            'is_favorite' => !$burger->is_favorite,
        ]);

        return redirect()->back();
    }
}