<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderStateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CourierController extends Controller
{
    public function __construct(private OrderStateService $state) {}

    public function toggleOnline(): JsonResponse
    {
        $user = auth()->user();
        $user->update(['courier_online' => !$user->courier_online]);

        return response()->json(['online' => $user->courier_online]);
    }

    public function dashboard(): Response
    {
        $user = auth()->user();

        $availableOrders = Order::where('status', Order::AWAITING_COURIER)
            ->with('items')
            ->latest()
            ->get()
            ->map(fn ($o) => $this->formatAvailableOrder($o));

        $myOrders = Order::where('courier_user_id', $user->id)
            ->whereIn('status', [Order::PICKED_UP, Order::DELIVERED])
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

        $isAssigned  = $order->courier_user_id === $user->id;
        $isAvailable = $order->status === Order::AWAITING_COURIER;

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

        $claimed = $this->state->claimAndPickUp($order, $user->id);

        if (!$claimed) {
            return response()->json(['success' => false, 'message' => 'Tellimus on juba teise kulleri poolt võetud.'], 409);
        }

        return response()->json(['success' => true]);
    }

    public function declineOrder(Order $order): JsonResponse
    {
        $user = auth()->user();

        $isAssigned  = $order->courier_user_id === $user->id;
        $isAvailable = $order->status === Order::AWAITING_COURIER;

        if (!$isAssigned && !$isAvailable && !$user->is_admin) {
            abort(403);
        }

        if ($order->status === Order::PICKED_UP && $isAssigned) {
            try {
                $this->state->unclaimOrder($order, $user->id);
            } catch (\RuntimeException $e) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
        }

        return response()->json(['success' => true]);
    }

    public function deliverOrder(Order $order): JsonResponse
    {
        $user = auth()->user();

        if ($order->courier_user_id !== $user->id && !$user->is_admin) {
            abort(403);
        }

        try {
            $this->state->markDelivered($order, $user->id);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

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

    /** Full order data for the assigned courier (includes delivery address). */
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

    /** Available-order data — delivery_address intentionally hidden until claimed. */
    private function formatAvailableOrder(Order $order): array
    {
        return [
            'id'               => $order->id,
            'order_number'     => $order->order_number,
            'status'           => $order->status,
            'total_amount'     => (float) $order->total_amount,
            'delivery_lat'     => null,
            'delivery_lng'     => null,
            'delivery_address' => null,
            'items'            => $order->items->map(fn ($item) => [
                'burger_name' => $item->burger_name,
                'quantity'    => $item->quantity,
                'price'       => (float) $item->price,
            ]),
        ];
    }
}
