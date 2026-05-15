<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderStateService;
use App\Services\PushNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CourierController extends Controller
{
    public function __construct(
        private OrderStateService $state,
        private PushNotificationService $push,
    ) {}

    public function toggleOnline(): JsonResponse
    {
        $user = auth()->user();
        $user->update(['courier_online' => !$user->courier_online]);

        return response()->json(['online' => $user->courier_online]);
    }

    public function dashboard(): Response
    {
        $user = auth()->user();

        $myOrders = Order::where('courier_user_id', $user->id)
            ->whereIn('status', [Order::PICKED_UP, Order::DELIVERED])
            ->with('items')
            ->latest()
            ->get()
            ->map(fn ($o) => $this->formatOrder($o));

        return Inertia::render('Courier/Dashboard', [
            'orders'        => $myOrders,
            'courierOnline' => (bool) $user->courier_online,
        ]);
    }

    /** JSON endpoint — polled as fallback when SSE is unavailable. */
    public function availableOrders(): JsonResponse
    {
        $orders = Order::where('status', Order::AWAITING_COURIER)
            ->with('items')
            ->orderByDesc('broadcasted_at')
            ->get()
            ->map(fn ($o) => $this->formatAvailableOrder($o));

        return response()->json($orders);
    }

    /** SSE stream — emits order:new and order:removed events every 2 s. */
    public function streamEvents(): StreamedResponse
    {
        return response()->stream(function () {
            // Release the session lock so concurrent requests aren't blocked.
            if (session()->isStarted()) {
                session()->save();
            }

            $knownIds = [];
            $deadline = time() + 50;
            $first    = true;

            while (time() < $deadline) {
                if (connection_aborted()) {
                    break;
                }

                $orders     = Order::where('status', Order::AWAITING_COURIER)->with('items')->get();
                $currentIds = $orders->pluck('id')->toArray();

                // Emit new orders (all on first tick; only additions on subsequent ticks).
                foreach ($orders as $order) {
                    if (!in_array($order->id, $knownIds)) {
                        echo "event: order:new\n";
                        echo 'data: ' . json_encode($this->formatAvailableOrder($order)) . "\n\n";
                        ob_flush();
                        flush();
                        $knownIds[] = $order->id;
                    }
                }

                // Emit removed orders (not on the very first tick).
                if (!$first) {
                    foreach (array_diff($knownIds, $currentIds) as $id) {
                        echo "event: order:removed\n";
                        echo 'data: ' . json_encode(['id' => $id]) . "\n\n";
                        ob_flush();
                        flush();
                        $knownIds = array_values(array_filter($knownIds, fn ($k) => $k !== $id));
                    }
                }

                $first = false;

                // Keepalive comment — also serves as a write that triggers connection_aborted() on the next loop.
                echo ": ping\n\n";
                ob_flush();
                flush();

                sleep(2);
            }
        }, 200, [
            'Content-Type'      => 'text/event-stream',
            'Cache-Control'     => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Connection'        => 'keep-alive',
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

        try {
            $this->push->sendToUser(
                $order->user_id,
                'Kuller on teel',
                "Tellimus {$order->order_number} on peale võetud ja teel sinu juurde!",
                '/orders/' . $order->id
            );
        } catch (\Throwable) {}

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

        try {
            $this->push->sendToUser(
                $order->user_id,
                'Tellimus kätte toimetatud',
                "Tellimus {$order->order_number} on kohale toimetatud. Naudi oma sööki!",
                '/orders/' . $order->id
            );
        } catch (\Throwable) {}

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
            'id'             => $order->id,
            'order_number'   => $order->order_number,
            'status'         => $order->status,
            'total_amount'   => (float) $order->total_amount,
            'broadcasted_at' => $order->broadcasted_at?->toIso8601String(),
            'items'          => $order->items->map(fn ($item) => [
                'burger_name' => $item->burger_name,
                'quantity'    => $item->quantity,
                'price'       => (float) $item->price,
            ]),
        ];
    }
}
