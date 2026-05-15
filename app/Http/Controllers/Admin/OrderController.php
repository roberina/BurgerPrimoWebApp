<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderStateService;
use App\Services\PushNotificationService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function __construct(
        private OrderStateService $state,
        private PushNotificationService $push,
    ) {}

    public function index(Request $request): Response
    {
        $query = Order::with(['user', 'items', 'confirmedBy', 'courierUser']);

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $orders = $query->latest()->paginate(20);

        $counts = Order::selectRaw('status, count(*) as count')->groupBy('status')->pluck('count', 'status');
        $stats = [
            'pending_confirmation' => $counts->get(Order::PENDING_CONFIRMATION, 0),
            'confirmed'            => $counts->get(Order::CONFIRMED, 0),
            'preparing'            => $counts->get(Order::PREPARING, 0),
            'ready'                => $counts->get(Order::READY, 0),
            'awaiting_courier'     => $counts->get(Order::AWAITING_COURIER, 0),
            'picked_up'            => $counts->get(Order::PICKED_UP, 0),
            'delivered'            => $counts->get(Order::DELIVERED, 0),
        ];

        return Inertia::render('Admin/Orders/Index', [
            'orders'  => $orders,
            'stats'   => $stats,
            'filters' => $request->only('status'),
        ]);
    }

    public function show(Order $order): Response
    {
        $order->load(['user', 'items', 'confirmedBy', 'courierUser', 'statusHistory.actor']);

        return Inertia::render('Admin/Orders/Show', [
            'order' => $order,
        ]);
    }

    public function confirm(Order $order)
    {
        try {
            $this->state->confirm($order, auth()->id());
        } catch (\RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        try {
            $this->push->sendToUser(
                $order->user_id,
                'Tellimus kinnitatud',
                "Tellimus {$order->order_number} on kinnitatud ja valmistamiseks järjekorras.",
                '/orders/' . $order->id
            );
        } catch (\Throwable) {}

        return redirect()->back()->with('success', 'Tellimus kinnitatud.');
    }

    public function startPreparing(Order $order)
    {
        try {
            $this->state->startPreparing($order, auth()->id());
        } catch (\RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        try {
            $this->push->sendToUser(
                $order->user_id,
                'Tellimust valmistatakse',
                "Tellimus {$order->order_number} on köögis valmistamisel.",
                '/orders/' . $order->id
            );
        } catch (\Throwable) {}

        return redirect()->back()->with('success', 'Valmistamine alustatud.');
    }

    public function markReady(Order $order)
    {
        try {
            $this->state->markReady($order, auth()->id());
        } catch (\RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        try {
            $this->push->sendToUser(
                $order->user_id,
                'Tellimus on valmis',
                "Tellimus {$order->order_number} on valmis ja ootab kullerit.",
                '/orders/' . $order->id
            );
        } catch (\Throwable) {}

        return redirect()->back()->with('success', 'Tellimus on valmis.');
    }

    public function releaseToCouriers(Order $order)
    {
        try {
            $this->state->releaseToCouriers($order, auth()->id());
        } catch (\RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        try {
            $this->push->sendToOnlineCouriers(
                'Uus tellimus saadaval',
                "Tellimus {$order->order_number} ootab kullerit",
                '/courier/dashboard'
            );
        } catch (\Throwable) {}

        return redirect()->back()->with('success', 'Tellimus saadetud kulleritele.');
    }

    public function recallFromCouriers(Order $order)
    {
        try {
            $this->state->recallFromCouriers($order, auth()->id());
        } catch (\RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->back()->with('success', 'Tellimus tagasi kutsutud.');
    }

    public function markCompleted(Order $order)
    {
        try {
            $this->state->markDeliveredByAdmin($order, auth()->id());
        } catch (\RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        try {
            $this->push->sendToUser(
                $order->user_id,
                'Tellimus kätte toimetatud',
                "Tellimus {$order->order_number} on kohale toimetatud. Naudi oma sööki!",
                '/orders/' . $order->id
            );
        } catch (\Throwable) {}

        return redirect()->back()->with('success', 'Tellimus märgitud lõpetatuks.');
    }

    public function refund(Request $request, Order $order)
    {
        $validated = $request->validate([
            'admin_notes' => 'nullable|string|max:500',
        ]);

        try {
            $this->state->refund($order, auth()->id(), $validated['admin_notes'] ?? '');
        } catch (\RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            \Log::error('Refund failed for order ' . $order->id . ': ' . $e->getMessage());
            return redirect()->back()->with('error', 'Tagasimakse ebaõnnestus: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Tellimus tagasi lükatud ja €' . number_format($order->total_amount, 2) . ' tagastatud kliendile.');
    }

    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'order_ids'   => 'required|array',
            'order_ids.*' => 'exists:orders,id',
        ]);

        $deleted = Order::whereIn('id', $validated['order_ids'])
            ->whereIn('status', [Order::DELIVERED, Order::CANCELLED, Order::REFUNDED])
            ->delete();

        return redirect()->back()->with('success', "{$deleted} tellimust kustutatud.");
    }
}
