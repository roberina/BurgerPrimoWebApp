<?php

namespace App\Http\Middleware;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        // Get cart count from session
        $cart = session()->get('cart', []);
        $cartCount = count($cart);

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user(),
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'courier_link' => fn () => $request->session()->get('courier_link'),
            ],
            'cartCount' => $cartCount,
            'deliveryStatus' => fn () => $this->getDeliveryStatus(),
        ]);
    }

    private function getDeliveryStatus(): array
    {
        $onlineCouriers = User::where('is_courier', true)
            ->where('courier_online', true)
            ->count();

        if ($onlineCouriers === 0) {
            return ['available' => false, 'couriers' => 0, 'eta' => null];
        }

        $activeDeliveries = Order::where('status', Order::PICKED_UP)->count();
        $freeCouriers = max(0, $onlineCouriers - $activeDeliveries);

        $baseEta = 20;
        $extraMin = $freeCouriers > 0 ? (int) ceil($activeDeliveries / $freeCouriers) * 5 : 30;
        $etaMin = $baseEta + $extraMin;
        $etaMax = $etaMin + 10;

        return [
            'available' => true,
            'couriers'  => $onlineCouriers,
            'eta'       => "{$etaMin}–{$etaMax} min",
        ];
    }
}