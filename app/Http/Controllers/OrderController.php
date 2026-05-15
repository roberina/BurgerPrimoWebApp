<?php

namespace App\Http\Controllers;

use App\Models\CustomBurger;
use App\Models\Order;
use App\Services\OrderStateService;
use App\Services\PushNotificationService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private OrderStateService $state,
        private PushNotificationService $push,
    ) {}

    const ORDER_HISTORY_LIMIT = 10;

    public function index(): Response
    {
        $user = auth()->user();

        // Delete oldest terminal orders that exceed the history limit
        $terminalStatuses = [Order::DELIVERED, Order::CANCELLED, Order::REFUNDED];
        $oldIds = $user->orders()
            ->latest()
            ->pluck('id')
            ->slice(self::ORDER_HISTORY_LIMIT);

        if ($oldIds->isNotEmpty()) {
            Order::whereIn('id', $oldIds)
                ->whereIn('status', $terminalStatuses)
                ->delete();
        }

        $orders = $user->orders()
            ->with('items')
            ->latest()
            ->limit(self::ORDER_HISTORY_LIMIT)
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

        if (in_array($order->status, ['cancelled', 'refunded'])) {
            $order->loadMissing('statusHistory');
            $cancelEntry = $order->statusHistory
                ->whereIn('to_status', ['cancelled', 'refunded'])
                ->sortByDesc('created_at')
                ->first();
            $order->setAttribute('cancelled_from_status', $cancelEntry?->from_status);
        }

        return Inertia::render('Orders/Show', [
            'order' => $order->makeHidden(['courier_token']),
        ]);
    }

    public function cancel(Order $order, Request $request)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        try {
            $this->state->cancel($order, auth()->id(), $validated['reason']);
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
        $terminalStatuses = [Order::DELIVERED, Order::CANCELLED, Order::REFUNDED];

        Order::where('user_id', auth()->id())
            ->whereIn('id', $orderIds)
            ->whereIn('status', $terminalStatuses)
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

    public function reorder(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $order->load('items');
        $cart = session()->get('cart', []);
        $added = 0;

        foreach ($order->items as $item) {
            if ($item->cart_data) {
                // Modern orders: full cart snapshot stored
                $data = $item->cart_data;
                if (isset($data['burger_id'])) {
                    $cartId = 'burger_' . $data['burger_id'] . '_' . time() . rand(100, 999);
                    $cart[$cartId] = [
                        'type'      => 'custom_burger',
                        'burger_id' => $data['burger_id'],
                        'quantity'  => $data['quantity'] ?? 1,
                        'subtotal'  => ((float) $item->price * ($data['quantity'] ?? 1)),
                    ];
                    $added++;
                } elseif (isset($data['menu_item_id'])) {
                    $cartId = uniqid('menu_', true);
                    $entry = $data;
                    $entry['id'] = $cartId;
                    $cart[] = $entry;
                    $added++;
                }
            } elseif ($item->custom_burger_id) {
                // Legacy custom burger (cart_data not yet stored)
                $cartId = 'burger_' . $item->custom_burger_id . '_' . time() . rand(100, 999);
                $cart[$cartId] = [
                    'type'      => 'custom_burger',
                    'burger_id' => $item->custom_burger_id,
                    'quantity'  => $item->quantity,
                    'subtotal'  => ((float) $item->price * $item->quantity),
                ];
                $added++;
            } elseif ($item->menu_item_id) {
                // Legacy menu item with ID stored but no cart snapshot
                $cart[] = $this->basicMenuCartEntry($item->menu_item_id, $item->burger_name, (float) $item->price, $item->quantity);
                $added++;
            } else {
                // Truly legacy: find menu item by name
                $menuItem = \App\Models\MenuItem::where('name', $item->burger_name)->where('is_active', true)->first();
                if ($menuItem) {
                    $cart[] = $this->basicMenuCartEntry($menuItem->id, $menuItem->name, (float) $menuItem->price, $item->quantity);
                    $added++;
                }
            }
        }

        if ($added === 0) {
            return redirect()->back()->with('error', 'Ühtegi toodet ei leitud – need võivad olla eemaldatud.');
        }

        session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('success', 'Tooted lisatud korvi!');
    }

    private function basicMenuCartEntry(int $menuItemId, string $name, float $price, int $quantity): array
    {
        return [
            'id'                   => uniqid('menu_', true),
            'menu_item_id'         => $menuItemId,
            'name'                 => $name,
            'base_price'           => $price,
            'size'                 => 'medium',
            'drinks'               => [],
            'sauces'               => [],
            'fries'                => null,
            'needs_utensils'       => false,
            'special_instructions' => '',
            'total_price'          => $price,
            'quantity'             => $quantity,
        ];
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

        try {
            $this->push->sendToAdmins(
                'Uus tellimus',
                "Tellimus {$order->order_number} – €" . number_format($totalAmount, 2),
                '/admin/orders/' . $order->id
            );
        } catch (\Throwable) {}

        return redirect()->route('orders.show', $order)
            ->with('success', 'Tellimus esitatud! Tellimuse number: ' . $order->order_number);
    }
}
