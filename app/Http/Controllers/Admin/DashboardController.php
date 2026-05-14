<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\CustomBurger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    private const SUBADMIN_REDIRECT_MAP = [
        'orders'        => '/admin/orders',
        'menu'          => '/admin/menu/categories',
        'burger'        => '/admin/ingredients',
        'users'         => '/admin/users',
        'announcements' => '/admin/announcements',
    ];

    public function index(): Response|RedirectResponse
    {
        $user = auth()->user();

        if ($user->admin_role === 'subadmin') {
            foreach ($user->admin_permissions ?? [] as $perm) {
                if (isset(self::SUBADMIN_REDIRECT_MAP[$perm])) {
                    return redirect(self::SUBADMIN_REDIRECT_MAP[$perm]);
                }
            }
            return redirect('/admin/users');
        }

        // Total revenue for CURRENT month only (delivered orders)
        $totalRevenue = Order::where('status', Order::DELIVERED)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_amount');

        // Total delivered orders for CURRENT month only
        $totalOrders = Order::where('status', Order::DELIVERED)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Growth percentage (compare CURRENT month to LAST month delivered orders)
        $lastMonthOrders = Order::where('status', Order::DELIVERED)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();
        
        $growthPercentage = $lastMonthOrders > 0 
            ? (($totalOrders - $lastMonthOrders) / $lastMonthOrders) * 100 
            : ($totalOrders > 0 ? 100 : 0);

        // Daily sales for bar chart (last 7 days)
        $dailySales = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $total = Order::where('status', Order::DELIVERED)
                ->whereDate('created_at', $date->toDateString())
                ->sum('total_amount');
            
            $dailySales->push([
                'day' => $date->format('N'), // 1 = Monday, 7 = Sunday
                'date' => $date->toDateString(),
                'total' => (float) $total,
            ]);
        }

        // Monthly orders for line chart (last 7 months)
        $monthlyOrders = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = Order::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            
            $monthlyOrders->push([
                'month' => (int) $date->format('m'),
                'year' => (int) $date->format('Y'),
                'count' => $count,
            ]);
        }

        // Popular products (most ordered burgers)
        $popularProducts = DB::table('order_items')
            ->select('burger_name', DB::raw('COUNT(*) as order_count'), DB::raw('SUM(quantity) as total_sold'))
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', '!=', Order::CANCELLED)
            ->where('orders.status', '!=', Order::REFUNDED)
            ->groupBy('burger_name')
            ->orderBy('total_sold', 'desc')
            ->limit(12)
            ->get()
            ->map(function ($item) {
                // Calculate average price
                $avgPrice = DB::table('order_items')
                    ->where('burger_name', $item->burger_name)
                    ->avg('price');
                
                return [
                    'name' => $item->burger_name,
                    'orders' => $item->order_count,
                    'sold' => $item->total_sold,
                    'price' => (float) $avgPrice,
                ];
            });

        // Ootavad burgerid adminile ülevaatamiseks
        $pendingBurgers = CustomBurger::where('status', 'pending')
            ->with(['user', 'ingredients'])
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($burger) {
                return [
                    'id'          => $burger->id,
                    'name'        => $burger->name,
                    'total_price' => (float) $burger->total_price,
                    'created_at'  => $burger->created_at->format('d.m.Y H:i'),
                    'user'        => [
                        'name'  => $burger->user->name,
                        'email' => $burger->user->email,
                    ],
                    'ingredients' => $burger->ingredients->map(fn($i) => [
                        'name'     => $i->name,
                        'category' => $i->category,
                        'quantity' => $i->pivot->quantity,
                        'price'    => (float) $i->price,
                    ]),
                ];
            });

        return Inertia::render('Admin/Dashboard/Index', [
            'stats' => [
                'totalRevenue' => (float) $totalRevenue,
                'totalOrders' => $totalOrders,
                'growthPercentage' => round($growthPercentage, 1),
            ],
            'dailySales' => $dailySales,
            'monthlyOrders' => $monthlyOrders,
            'popularProducts' => $popularProducts,
            'pendingBurgers' => $pendingBurgers,
        ]);
    }
}