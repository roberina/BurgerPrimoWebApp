<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function index(Request $request): Response
    {

        $query = Order::with(['user', 'items', 'confirmedBy', 'courierUser']);

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $orders = $query->latest()->paginate(20);

        $stats = [
            'pending' => Order::where('status', 'pending')->count(),
            'confirmed' => Order::where('status', 'confirmed')->count(),
            'preparing' => Order::where('status', 'preparing')->count(),
            'ready' => Order::where('status', 'ready')->count(),
            'awaiting_courier' => Order::where('status', 'awaiting_courier')->count(),
            'delivering' => Order::where('status', 'delivering')->count(),
            'completed' => Order::where('status', 'completed')->count(),
        ];

        return Inertia::render('Admin/Orders/Index', [
            'orders' => $orders,
            'stats' => $stats,
            'filters' => $request->only('status'),
        ]);
    }

    public function show(Order $order): Response
    {

        $order->load(['user', 'items', 'confirmedBy']);

        return Inertia::render('Admin/Orders/Show', [
            'order' => $order,
        ]);
    }

    public function updateStatus(Request $request, Order $order)
    {

        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,preparing,ready,awaiting_courier,delivering,completed,cancelled',
            'admin_notes' => 'nullable|string|max:500',
        ]);

        $updateData = ['status' => $validated['status']];

        // If confirming for the first time
        if ($validated['status'] === 'confirmed' && $order->status === 'pending') {
            $updateData['confirmed_at'] = now();
            $updateData['confirmed_by'] = auth()->id();
        }

        if (isset($validated['admin_notes'])) {
            $updateData['admin_notes'] = $validated['admin_notes'];
        }   

        $order->update($updateData);

        return redirect()->back()->with('success', 'Order status updated successfully!');
    }

    public function confirm(Order $order)
    {

        if ($order->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending orders can be confirmed.');
        }

        $order->confirm(auth()->id());

        return redirect()->back()->with('success', 'Order confirmed successfully!');
    }

    public function bulkUpdateStatus(Request $request)
    {

        $validated = $request->validate([
            'order_ids' => 'required|array',
            'order_ids.*' => 'exists:orders,id',
            'status' => 'required|in:confirmed,preparing,ready,delivering,completed,cancelled',
        ]);

        Order::whereIn('id', $validated['order_ids'])
            ->update(['status' => $validated['status']]);

        return redirect()->back()->with('success', 'Orders updated successfully!');
    }

public function reject(Request $request, Order $order)
{
    $validated = $request->validate([
        'admin_notes' => 'nullable|string|max:500',
    ]);

    // If order was paid with card via Stripe, process refund
    if ($order->payment_status === 'succeeded' && $order->payment_intent_id) {
        try {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret_key'));
            
            // Create full refund in Stripe
            $refund = \Stripe\Refund::create([
                'payment_intent' => $order->payment_intent_id,
                'reason' => 'requested_by_customer',
            ]);
            
            // Update order with rejection and refund status
            $order->update([
                'status' => 'rejected',
                'payment_status' => 'refunded',
                'admin_notes' => $validated['admin_notes'] ?? 'Tellimus lükati tagasi ja makse tagastati',
            ]);
            
            return redirect()->back()->with('success', 'Tellimus tagasi lükatud ja €' . number_format($order->total_amount, 2) . ' tagastatud kliendile!');
            
        } catch (\Exception $e) {
            \Log::error('Refund failed: ' . $e->getMessage());
            $order->update([
                'status' => 'rejected',
                'admin_notes' => $validated['admin_notes'] ?? 'Tellimus lükati tagasi (tagasimakse ebaõnnestus)',
            ]);
            
            return redirect()->back()->with('error', 'Tellimus tagasi lükatud, kuid tagasimakse ebaõnnestus: ' . $e->getMessage());
        }
    }
    
    // If order wasn't paid via Stripe, just reject it
    $order->update([
        'status' => 'rejected',
        'admin_notes' => $validated['admin_notes'] ?? 'Tellimus lükati tagasi',
    ]);
    
    return redirect()->back()->with('success', 'Order rejected successfully!');
}

    /**
     * Mark order as delivering and generate a unique courier tracking token.
     * Returns the courier link so admin can send it to the courier.
     */
    public function startDelivery(Request $request, Order $order)
    {
        if ($order->status !== 'ready') {
            return redirect()->back()->with('error', 'Ainult valmis tellimusi saab kullerile saata.');
        }

        $order->update([
            'status'          => 'awaiting_courier',
            'courier_user_id' => null,
        ]);

        return redirect()->back()->with('success', 'Tellimus saadetud kulleritele.');
    }

    /**
     * Bulk delete completed orders
     */
    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'order_ids' => 'required|array',
            'order_ids.*' => 'exists:orders,id',
        ]);

        // Only allow deletion of completed orders for safety
        $deletedCount = Order::whereIn('id', $validated['order_ids'])
            ->where('status', 'completed')
            ->where('user_id', auth()->id()) // Ensure user can only delete their own orders
            ->delete();

        return redirect()->back()->with('success', "{$deletedCount} order(s) deleted successfully!");
    }
}