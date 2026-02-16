<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IngredientController extends Controller
{
    /**
     * Display a listing of ingredients
     */
    public function index(): Response
    {
        $ingredients = Ingredient::orderBy('category')
            ->orderBy('name')
            ->get()
            ->groupBy('category');

        $stats = [
            'total' => Ingredient::count(),
            'buns' => Ingredient::where('category', 'buns')->count(),
            'patties' => Ingredient::where('category', 'patties')->count(),
            'vegetables' => Ingredient::where('category', 'vegetables')->count(),
            'sauces' => Ingredient::where('category', 'sauces')->count(),
            'extras' => Ingredient::where('category', 'extras')->count(),
        ];

        return Inertia::render('Admin/Ingredients/Index', [
            'ingredients' => $ingredients,
            'stats' => $stats,
        ]);
    }

    /**
     * Show the form for creating a new ingredient
     */
    public function create(): Response
    {
        return Inertia::render('Admin/Ingredients/Create');
    }

    /**
     * Store a newly created ingredient
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:buns,patties,vegetables,sauces,extras',
            'price' => 'required|numeric|min:0|max:99.99',
            'is_available' => 'boolean',
        ]);

        Ingredient::create($validated);

        return redirect()->route('admin.ingredients.index')
            ->with('success', 'Koostisosa edukalt lisatud!');
    }

    /**
     * Show the form for editing an ingredient
     */
    public function edit(Ingredient $ingredient): Response
    {
        return Inertia::render('Admin/Ingredients/Edit', [
            'ingredient' => $ingredient,
        ]);
    }

    /**
     * Update the specified ingredient
     */
    public function update(Request $request, Ingredient $ingredient)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:buns,patties,vegetables,sauces,extras',
            'price' => 'required|numeric|min:0|max:99.99',
            'is_available' => 'boolean',
        ]);

        $ingredient->update($validated);

        return redirect()->route('admin.ingredients.index')
            ->with('success', 'Koostisosa edukalt uuendatud!');
    }

    /**
     * Remove the specified ingredient
     */
    public function destroy(Ingredient $ingredient)
    {
        $ingredient->delete();

        return redirect()->route('admin.ingredients.index')
            ->with('success', 'Koostisosa edukalt kustutatud!');
    }

    /**
     * Toggle ingredient availability
     */
    public function toggleAvailability(Ingredient $ingredient)
    {
        $ingredient->update([
            'is_available' => !$ingredient->is_available,
        ]);

        return redirect()->back()
            ->with('success', 'Saadavus uuendatud!');
    }
}