<?php

namespace App\Http\Controllers;

use App\Models\CustomBurger;
use App\Models\Order;
use App\Services\OrderStateService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private OrderStateService $state) {}

    public function index(): Response
    {
        $orders = auth()->user()
            ->orders()
            ->with('items')
            ->latest()
            ->get()
            ->makeHidden(['courier_token']);

        return Inertia::render('Orders/Index', [
            'orders' => $orders,
        ]);
    }

    public function show(Order $order): Response
    {
        if ($order->user_id !== auth()->id() && !auth()->user()->is_admin) {
            abort(403, 'Unauthorized');
        }

        $order->load(['items', 'confirmedBy']);

        return Inertia::render('Orders/Show', [
            'order' => $order->makeHidden(['courier_token']),
        ]);
    }

    public function cancel(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        try {
            $this->state->cancel($order, auth()->id());
        } catch (\RuntimeException $e) {
            return redirect()->back()->with('error', 'Tellimust ei saa enam tühistada.');
        }

        return redirect()->back()->with('success', 'Tellimus tühistatud.');
    }

    public function destroy(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        if (!in_array($order->status, [Order::REFUNDED, Order::CANCELLED])) {
            return redirect()->back()->with('error', 'Ainult tühistatud või tagastatud tellimusi saab kustutada.');
        }

        $order->delete();

        return redirect()->back()->with('success', 'Tellimus eemaldatud.');
    }

    public function bulkDelete(Request $request)
    {
        $orderIds = $request->input('order_ids', []);

        Order::where('user_id', auth()->id())
            ->whereIn('id', $orderIds)
            ->where('status', Order::DELIVERED)
            ->delete();

        return redirect()->back();
    }

    public function updateDeliveryLocation(Request $request, Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $activeStatuses = [
            Order::PENDING_CONFIRMATION,
            Order::CONFIRMED,
            Order::PREPARING,
            Order::READY,
            Order::PICKED_UP,
        ];

        if (!in_array($order->status, $activeStatuses)) {
            return redirect()->back()->with('error', 'Selle tellimuse sihtkohta ei saa enam muuta.');
        }

        $validated = $request->validate([
            'delivery_lat'     => 'required|numeric|between:57.85,58.75',
            'delivery_lng'     => 'required|numeric|between:21.50,23.20',
            'delivery_address' => 'required|string|max:500',
        ]);

        $order->update($validated);

        return redirect()->back();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'burgers'                 => 'required|array|min:1',
            'burgers.*.burger_id'     => 'required|exists:custom_burgers,id',
            'burgers.*.quantity'      => 'required|integer|min:1|max:10',
            'customer_notes'          => 'nullable|string|max:500',
        ]);

        $order = Order::create([
            'user_id'        => auth()->id(),
            'order_number'   => Order::generateOrderNumber(),
            'total_amount'   => 0,
            'status'         => Order::PENDING_CONFIRMATION,
            'customer_notes' => $validated['customer_notes'] ?? null,
        ]);

        $totalAmount = 0;

        foreach ($validated['burgers'] as $burgerData) {
            $burger = CustomBurger::with('ingredients')->findOrFail($burgerData['burger_id']);

            if ($burger->user_id !== auth()->id()) {
                abort(403, 'Unauthorized');
            }

            $price    = $burger->total_price;
            $quantity = $burgerData['quantity'];
            $totalAmount += $price * $quantity;

            $order->items()->create([
                'custom_burger_id' => $burger->id,
                'burger_name'      => $burger->name,
                'ingredients'      => $burger->ingredients->map(fn ($i) => [
                    'name'     => $i->name,
                    'category' => $i->category,
                    'quantity' => $i->pivot->quantity,
                    'price'    => $i->price,
                ])->toArray(),
                'price'    => $price,
                'quantity' => $quantity,
            ]);
        }

        $order->update(['total_amount' => $totalAmount]);

        return redirect()->route('orders.show', $order)
            ->with('success', 'Tellimus esitatud! Tellimuse number: ' . $order->order_number);
    }
}
