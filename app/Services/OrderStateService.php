<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderStatusHistory;
use Illuminate\Support\Facades\Log;

class OrderStateService
{
    // Valid statuses
    const PENDING_CONFIRMATION = 'pending_confirmation';
    const CONFIRMED            = 'confirmed';
    const PREPARING            = 'preparing';
    const READY                = 'ready';
    const AWAITING_COURIER     = 'awaiting_courier';
    const PICKED_UP            = 'picked_up';
    const DELIVERED            = 'delivered';
    const CANCELLED            = 'cancelled';
    const REFUNDED             = 'refunded';

    // Statuses that can still be refunded (order not yet in transit)
    const REFUNDABLE_STATUSES = [
        self::PENDING_CONFIRMATION,
        self::CONFIRMED,
        self::PREPARING,
        self::READY,
        self::AWAITING_COURIER,
    ];

    private function transition(Order $order, string $to, ?int $actorId, string $actorType, array $extra = [], ?string $note = null): void
    {
        $from = $order->status;

        $order->update(array_merge(['status' => $to], $extra));

        OrderStatusHistory::create([
            'order_id'   => $order->id,
            'from_status' => $from,
            'to_status'   => $to,
            'actor_id'   => $actorId,
            'actor_type' => $actorType,
            'note'       => $note,
        ]);
    }

    public function confirm(Order $order, int $adminId): void
    {
        if ($order->status !== self::PENDING_CONFIRMATION) {
            throw new \RuntimeException("Cannot confirm order in status: {$order->status}");
        }

        $this->transition($order, self::CONFIRMED, $adminId, 'admin', [
            'confirmed_at' => now(),
            'confirmed_by' => $adminId,
        ]);
    }

    public function startPreparing(Order $order, int $adminId): void
    {
        if ($order->status !== self::CONFIRMED) {
            throw new \RuntimeException("Cannot start preparing order in status: {$order->status}");
        }

        $this->transition($order, self::PREPARING, $adminId, 'admin');
    }

    public function markReady(Order $order, int $adminId): void
    {
        if ($order->status !== self::PREPARING) {
            throw new \RuntimeException("Cannot mark ready order in status: {$order->status}");
        }

        $this->transition($order, self::READY, $adminId, 'admin');
    }

    public function releaseToCouriers(Order $order, int $adminId): void
    {
        if ($order->status !== self::READY) {
            throw new \RuntimeException("Cannot release order in status: {$order->status}");
        }

        $this->transition($order, self::AWAITING_COURIER, $adminId, 'admin', [
            'courier_user_id' => null,
            'broadcasted_at'  => now(),
        ]);
    }

    public function recallFromCouriers(Order $order, int $adminId): void
    {
        if ($order->status !== self::AWAITING_COURIER) {
            throw new \RuntimeException("Cannot recall order in status: {$order->status}");
        }

        $this->transition($order, self::READY, $adminId, 'admin');
    }

    /**
     * Atomic claim: only succeeds if the order is still awaiting a courier.
     * Returns true on success, false if another courier got there first.
     */
    public function claimAndPickUp(Order $order, int $courierId): bool
    {
        $updated = Order::where('id', $order->id)
            ->where('status', self::AWAITING_COURIER)
            ->update([
                'status'          => self::PICKED_UP,
                'courier_user_id' => $courierId,
            ]);

        if (!$updated) {
            return false;
        }

        OrderStatusHistory::create([
            'order_id'    => $order->id,
            'from_status' => self::AWAITING_COURIER,
            'to_status'   => self::PICKED_UP,
            'actor_id'    => $courierId,
            'actor_type'  => 'courier',
        ]);

        return true;
    }

    public function markDelivered(Order $order, int $courierId): void
    {
        if ($order->status !== self::PICKED_UP) {
            throw new \RuntimeException("Cannot mark delivered order in status: {$order->status}");
        }

        $this->transition($order, self::DELIVERED, $courierId, 'courier', [
            'courier_updated_at' => now(),
        ]);
    }

    public function markDeliveredByAdmin(Order $order, int $adminId): void
    {
        if (!in_array($order->status, [self::READY, self::PICKED_UP])) {
            throw new \RuntimeException("Cannot mark completed order in status: {$order->status}");
        }

        $this->transition($order, self::DELIVERED, $adminId, 'admin', [
            'courier_updated_at' => now(),
        ]);
    }

    public function unclaimOrder(Order $order, int $courierId): void
    {
        if ($order->status !== self::PICKED_UP || $order->courier_user_id !== $courierId) {
            throw new \RuntimeException("Cannot unclaim order in status: {$order->status}");
        }

        $this->transition($order, self::AWAITING_COURIER, $courierId, 'courier', [
            'courier_user_id'    => null,
            'courier_lat'        => null,
            'courier_lng'        => null,
            'courier_updated_at' => null,
        ]);
    }

    /**
     * Admin refund: any pre-transit status → refunded (with Stripe if applicable).
     */
    public function refund(Order $order, int $adminId, string $reason = ''): void
    {
        if (!in_array($order->status, self::REFUNDABLE_STATUSES)) {
            throw new \RuntimeException("Cannot refund order in status: {$order->status}");
        }

        if ($order->payment_status === 'succeeded' && $order->payment_intent_id) {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret_key'));
            \Stripe\Refund::create([
                'payment_intent' => $order->payment_intent_id,
                'reason'         => 'requested_by_customer',
            ]);

            $order->update(['payment_status' => 'refunded']);
        }

        $this->transition($order, self::REFUNDED, $adminId, 'admin', [
            'admin_notes' => $reason ?: 'Tellimus tagasi lükatud',
        ], $reason ?: null);
    }

    /**
     * Customer cancel: pending_confirmation|confirmed → cancelled (refund if paid).
     */
    public function cancel(Order $order, int $customerId, string $reason = ''): void
    {
        if (!in_array($order->status, [self::PENDING_CONFIRMATION, self::CONFIRMED])) {
            throw new \RuntimeException("Cannot cancel order in status: {$order->status}");
        }

        if ($order->payment_status === 'succeeded' && $order->payment_intent_id) {
            try {
                \Stripe\Stripe::setApiKey(config('services.stripe.secret_key'));
                \Stripe\Refund::create([
                    'payment_intent' => $order->payment_intent_id,
                    'reason'         => 'requested_by_customer',
                ]);
                $order->update(['payment_status' => 'refunded']);
            } catch (\Exception $e) {
                Log::error('Customer cancel refund failed for order ' . $order->id . ': ' . $e->getMessage());
            }
        }

        if ($reason) {
            $order->update(['cancellation_reason' => $reason]);
        }

        $this->transition($order, self::CANCELLED, $customerId, 'customer', [], $reason ?: null);
    }
}
