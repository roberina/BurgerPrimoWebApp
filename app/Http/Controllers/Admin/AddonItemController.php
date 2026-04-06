<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AddonItem;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AddonItemController extends Controller
{
    public function index()
    {
        $grouped = collect(AddonItem::TYPES)->mapWithKeys(function ($type) {
            return [
                $type => AddonItem::ofType($type)->ordered()->get(),
            ];
        });

        return Inertia::render('Admin/Addons/Index', [
            'grouped'    => $grouped,
            'typeLabels' => AddonItem::TYPE_LABELS,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type'         => 'required|in:' . implode(',', AddonItem::TYPES),
            'name'         => 'required|string|max:120',
            'price'        => 'required|numeric|min:0|max:999',
            'slug'         => 'nullable|string|max:60',
            'is_available' => 'boolean',
            'sort_order'   => 'integer|min:0',
        ]);

        AddonItem::create($data);

        return redirect()->back()->with('success', 'Lisand edukalt lisatud!');
    }

    public function update(Request $request, AddonItem $addonItem)
    {
        $data = $request->validate([
            'type'         => 'required|in:' . implode(',', AddonItem::TYPES),
            'name'         => 'required|string|max:120',
            'price'        => 'required|numeric|min:0|max:999',
            'slug'         => 'nullable|string|max:60',
            'is_available' => 'boolean',
            'sort_order'   => 'integer|min:0',
        ]);

        $addonItem->update($data);

        return redirect()->back()->with('success', 'Lisand edukalt uuendatud!');
    }

    public function destroy(AddonItem $addonItem)
    {
        $addonItem->delete();

        return redirect()->back()->with('success', 'Lisand edukalt kustutatud!');
    }

    public function toggle(AddonItem $addonItem)
    {
        $addonItem->update(['is_available' => ! $addonItem->is_available]);

        return redirect()->back()->with('success',
            $addonItem->is_available ? 'Lisand aktiveeritud!' : 'Lisand peidetud!'
        );
    }

    /**
     * Public API endpoint: return all available addons grouped by type.
     * Called by the MenuItem addon modal via fetch().
     */
    public function publicIndex()
    {
        $grouped = collect(AddonItem::TYPES)->mapWithKeys(function ($type) {
            return [
                $type => AddonItem::ofType($type)->available()->ordered()->get(['id','name','price','slug']),
            ];
        });

        return response()->json($grouped);
    }
}
