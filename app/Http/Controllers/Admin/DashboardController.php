<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\MenuCategory;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        // ── Menu stats ──────────────────────────────────────────────────
        $totalItems       = MenuItem::count();
        $totalCategories  = MenuCategory::count();
        $featuredItems    = MenuItem::where('is_featured', true)->count();
        $unavailableItems = MenuItem::where('is_available', false)->count();

        // ── Revenue (current month, completed orders) ────────────────────
        $totalRevenue = Order::where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_amount');

        $totalOrders = Order::where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $lastMonthOrders = Order::where('status', 'completed')
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();

        $growthPercentage = $lastMonthOrders > 0
            ? round((($totalOrders - $lastMonthOrders) / $lastMonthOrders) * 100, 1)
            : ($totalOrders > 0 ? 100 : 0);

        // ── Daily sales last 7 days ──────────────────────────────────────
        $dailySales = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dailySales->push([
                'day'   => (int) $date->format('N'),
                'date'  => $date->toDateString(),
                'total' => (float) Order::where('status', 'completed')
                    ->whereDate('created_at', $date->toDateString())
                    ->sum('total_amount'),
            ]);
        }

        // ── Monthly orders last 7 months ─────────────────────────────────
        $monthlyOrders = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyOrders->push([
                'month' => (int) $date->format('m'),
                'year'  => (int) $date->format('Y'),
                'count' => Order::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
            ]);
        }

        // ── Popular products ─────────────────────────────────────────────
        $popularProducts = DB::table('order_items')
            ->select('burger_name', DB::raw('COUNT(*) as order_count'), DB::raw('SUM(quantity) as total_sold'))
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereNotIn('orders.status', ['cancelled', 'rejected'])
            ->groupBy('burger_name')
            ->orderByDesc('total_sold')
            ->limit(8)
            ->get()
            ->map(fn ($item) => [
                'name'   => $item->burger_name,
                'orders' => $item->order_count,
                'sold'   => $item->total_sold,
                'price'  => (float) DB::table('order_items')->where('burger_name', $item->burger_name)->avg('price'),
            ]);

        // ── Pending orders count ─────────────────────────────────────────
        $pendingOrders = Order::where('status', 'pending')->count();

        // ── Recent menu items ────────────────────────────────────────────
        $recentItems = MenuItem::with('category')
            ->latest()
            ->take(6)
            ->get()
            ->map(fn ($item) => [
                'id'           => $item->id,
                'name'         => $item->name,
                'price'        => (float) $item->price,
                'is_active'    => (bool) $item->is_active,
                'is_featured'  => (bool) $item->is_featured,
                'is_available' => (bool) $item->is_available,
                'image_url'    => $item->image_url,
                'category'     => $item->category ? ['name' => $item->category->name] : null,
            ]);

        return Inertia::render('Admin/Dashboard/Index', [
            'stats' => [
                'totalItems'       => $totalItems,
                'totalCategories'  => $totalCategories,
                'featuredItems'    => $featuredItems,
                'unavailableItems' => $unavailableItems,
                'totalRevenue'     => (float) $totalRevenue,
                'totalOrders'      => $totalOrders,
                'growthPercentage' => $growthPercentage,
                'pendingOrders'    => $pendingOrders,
            ],
            'recentItems'     => $recentItems,
            'dailySales'      => $dailySales,
            'monthlyOrders'   => $monthlyOrders,
            'popularProducts' => $popularProducts,
        ]);
    }
}