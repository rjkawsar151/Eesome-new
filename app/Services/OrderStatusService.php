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

            if ($toStatus === 'Cancelled') {
                $order->load('items');
                foreach ($order->items as $item) {
                    $product = \App\Models\Product::find($item->product_id);
                    if ($product && !$product->available_for_preorder) {
                        $stockBefore = $product->stock;
                        $product->increment('stock', $item->quantity);
                        $product->refresh();

                        \App\Models\InventoryMovement::create([
                            'product_id'     => $product->id,
                            'order_id'       => $order->id,
                            'type'           => 'cancel_return',
                            'quantity_delta' => $item->quantity,
                            'stock_before'   => $stockBefore,
                            'stock_after'    => $product->stock,
                            'reference'      => $order->order_number,
                            'created_by_user_id' => Auth::id(),
                        ]);
                    }
                }
            }
        });

        // Dispatch notification AFTER commit
        // event(new \App\Events\OrderStatusChanged($order));
    }
}
