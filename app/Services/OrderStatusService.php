<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderStatusHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderStatusService
{
    // Allowed status transitions
    private const TRANSITIONS = [
        'Pending'    => ['Processing', 'Cancelled'],
        'Processing' => ['Shipped', 'Cancelled'],
        'Shipped'    => ['Delivered'],
        'Delivered'  => [],
        'Cancelled'  => [],
    ];

    public function canTransition(string $fromStatus, string $toStatus): bool
    {
        return in_array($toStatus, self::TRANSITIONS[$fromStatus] ?? []);
    }

    public function getAllowedNext(string $fromStatus): array
    {
        return self::TRANSITIONS[$fromStatus] ?? [];
    }

    public function transition(Order $order, string $toStatus, ?string $note = null): void
    {
        $fromStatus = $order->order_status;

        if (!$this->canTransition($fromStatus, $toStatus)) {
            throw new \InvalidArgumentException(
                "Cannot transition order #{$order->order_number} from '{$fromStatus}' to '{$toStatus}'."
            );
        }

        DB::transaction(function () use ($order, $fromStatus, $toStatus, $note) {
            $order->order_status = $toStatus;
            $order->status_changed_at = now();
            $order->save();

            OrderStatusHistory::create([
                'order_id'            => $order->id,
                'from_status'         => $fromStatus,
                'to_status'           => $toStatus,
                'changed_by_user_id'  => Auth::id(),
                'note'                => $note,
            ]);
        });

        // Dispatch notification AFTER commit
        // event(new \App\Events\OrderStatusChanged($order));
    }
}
