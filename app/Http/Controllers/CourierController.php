<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CourierController extends Controller
{
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
