<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CourierController extends Controller
{
    // ─── Account-based methods ────────────────────────────────

    public function toggleOnline(): JsonResponse
    {
        $user = auth()->user();

        if (!$user->is_courier && !$user->is_admin) {
            abort(403);
        }

        $user->update(['courier_online' => !$user->courier_online]);

        return response()->json(['online' => $user->courier_online]);
    }

    public function dashboard(): Response
    {
        $user = auth()->user();

        if (!$user->is_courier && !$user->is_admin) {
            abort(403);
        }

        $availableOrders = Order::where('status', 'awaiting_courier')
            ->with('items')
            ->latest()
            ->get()
            ->map(fn ($o) => $this->formatOrder($o));

        $myOrders = Order::where('courier_user_id', $user->id)
            ->whereIn('status', ['delivering', 'completed'])
            ->with('items')
            ->latest()
            ->get()
            ->map(fn ($o) => $this->formatOrder($o));

        return Inertia::render('Courier/Dashboard', [
            'orders'          => $myOrders,
            'availableOrders' => $availableOrders,
            'courierOnline'   => (bool) $user->courier_online,
        ]);
    }

    public function showOrder(Order $order): Response
    {
        $user = auth()->user();

        $isAssigned = $order->courier_user_id === $user->id;
        $isAvailable = $order->status === 'awaiting_courier';

        if (!$isAssigned && !$isAvailable && !$user->is_admin) {
            abort(403);
        }

        $order->load('items');

        return Inertia::render('Courier/Track', [
            'order'        => $this->formatOrder($order),
            'updateUrl'    => route('courier.order.location', $order),
            'acceptUrl'    => route('courier.order.accept', $order),
            'declineUrl'   => route('courier.order.decline', $order),
            'deliveredUrl' => route('courier.order.delivered', $order),
            'dashboardUrl' => route('courier.dashboard'),
        ]);
    }

    public function acceptOrder(Order $order): JsonResponse
    {
        $user = auth()->user();

        if (!$user->is_courier && !$user->is_admin) {
            abort(403);
        }

        // Atomic update — only succeeds if order is still awaiting a courier
        $updated = Order::where('id', $order->id)
            ->where('status', 'awaiting_courier')
            ->update([
                'status'          => 'delivering',
                'courier_user_id' => $user->id,
            ]);

        if (!$updated) {
            return response()->json(['success' => false, 'message' => 'Tellimus on juba teise kulleri poolt võetud.'], 409);
        }

        return response()->json(['success' => true]);
    }

    public function declineOrder(Order $order): JsonResponse
    {
        $user = auth()->user();

        $isAssigned = $order->courier_user_id === $user->id;
        $isAvailable = $order->status === 'awaiting_courier';

        if (!$isAssigned && !$isAvailable && !$user->is_admin) {
            abort(403);
        }

        // If courier already accepted and then declines, put back to awaiting_courier
        if ($order->status === 'delivering' && $isAssigned) {
            $order->update([
                'status'             => 'awaiting_courier',
                'courier_user_id'    => null,
                'courier_lat'        => null,
                'courier_lng'        => null,
                'courier_updated_at' => null,
            ]);
        }
        // If still awaiting, just return — order remains available for others

        return response()->json(['success' => true]);
    }

    public function deliverOrder(Order $order): JsonResponse
    {
        $user = auth()->user();

        if ($order->courier_user_id !== $user->id && !$user->is_admin) {
            abort(403);
        }

        $order->update([
            'status'             => 'completed',
            'courier_updated_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    public function updateOrderLocation(Request $request, Order $order): JsonResponse
    {
        $user = auth()->user();

        if ($order->courier_user_id !== $user->id && !$user->is_admin) {
            abort(403);
        }

        $validated = $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
        ]);

        $order->update([
            'courier_lat'        => $validated['lat'],
            'courier_lng'        => $validated['lng'],
            'courier_updated_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    private function formatOrder(Order $order): array
    {
        return [
            'id'               => $order->id,
            'order_number'     => $order->order_number,
            'status'           => $order->status,
            'total_amount'     => (float) $order->total_amount,
            'delivery_lat'     => $order->delivery_lat,
            'delivery_lng'     => $order->delivery_lng,
            'delivery_address' => $order->delivery_address,
            'items'            => $order->items->map(fn ($item) => [
                'burger_name' => $item->burger_name,
                'quantity'    => $item->quantity,
                'price'       => (float) $item->price,
            ]),
        ];
    }

    // ─── Token-based methods (legacy) ────────────────────────

    /**
     * Show the courier tracking page (runs on courier's phone).
     * Accessible via unique token — no login required.
     */
    public function track(string $token): Response
    {
        $order = Order::where('courier_token', $token)
            ->whereIn('status', ['delivering'])
            ->with('items')
            ->firstOrFail();

        return Inertia::render('Courier/Track', [
            'order' => [
                'id'               => $order->id,
                'order_number'     => $order->order_number,
                'status'           => $order->status,
                'total_amount'     => (float) $order->total_amount,
                'delivery_lat'     => $order->delivery_lat,
                'delivery_lng'     => $order->delivery_lng,
                'delivery_address' => $order->delivery_address,
                'items'            => $order->items->map(fn ($item) => [
                    'burger_name' => $item->burger_name,
                    'quantity'    => $item->quantity,
                    'price'       => (float) $item->price,
                ]),
            ],
            'token'        => $token,
            'updateUrl'    => url("/courier/location/{$token}"),
            'acceptUrl'    => url("/courier/accept/{$token}"),
            'declineUrl'   => url("/courier/decline/{$token}"),
            'deliveredUrl' => url("/courier/delivered/{$token}"),
        ]);
    }

    /**
     * Courier accepts the delivery — nothing changes in DB, just confirms intent.
     */
    public function accept(string $token): JsonResponse
    {
        Order::where('courier_token', $token)
            ->whereIn('status', ['delivering'])
            ->firstOrFail();

        return response()->json(['success' => true]);
    }

    /**
     * Courier declines the delivery — order goes back to 'ready'.
     */
    public function decline(string $token): JsonResponse
    {
        $order = Order::where('courier_token', $token)
            ->whereIn('status', ['delivering'])
            ->firstOrFail();

        $order->update([
            'status'            => 'ready',
            'courier_token'     => null,
            'courier_lat'       => null,
            'courier_lng'       => null,
            'courier_updated_at' => null,
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Courier marks the order as delivered.
     */
    public function delivered(string $token): JsonResponse
    {
        $order = Order::where('courier_token', $token)
            ->whereIn('status', ['delivering'])
            ->firstOrFail();

        $order->update([
            'status'             => 'completed',
            'courier_token'      => null,
            'courier_updated_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Receive GPS coordinates from the courier's phone.
     */
    public function updateLocation(Request $request, string $token): JsonResponse
    {
        $validated = $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
        ]);

        $order = Order::where('courier_token', $token)
            ->whereIn('status', ['delivering'])
            ->firstOrFail();

        $order->update([
            'courier_lat'        => $validated['lat'],
            'courier_lng'        => $validated['lng'],
            'courier_updated_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }
}
