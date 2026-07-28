<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\InventoryMovement;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\Product;
use App\Notifications\OrderStatusUpdated;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class OrderStatusService
{
    public function canTransition(string $from, string $to): bool
    {
        $current = OrderStatus::tryFrom(strtolower($from));
        $target = OrderStatus::tryFrom(strtolower($to));

        return $current && $target && in_array($target, $current->next(), true);
    }

    public function getAllowedNext(string $from): array
    {
        $status = OrderStatus::tryFrom(strtolower($from));

        return array_map(fn ($s) => $s->value, $status?->next() ?? []);
    }

    public function transition(Order $order, string $toStatus, ?string $note = null, array $shipment = []): void
    {
        $from = strtolower($order->order_status);
        $to = strtolower($toStatus);
        if (! $this->canTransition($from, $to)) {
            throw new \InvalidArgumentException("Cannot transition order #{$order->order_number} from '{$from}' to '{$to}'.");
        }DB::transaction(function () use ($order, $from, $to, $note, $shipment) {
            $order->fill(array_filter(['order_status' => $to, 'status_changed_at' => now(), 'shipping_provider' => $shipment['shipping_provider'] ?? null, 'tracking_number' => $shipment['tracking_number'] ?? null, 'tracking_url' => $shipment['tracking_url'] ?? null, 'estimated_delivery_at' => $shipment['estimated_delivery_at'] ?? null], fn ($v) => $v !== null));
            if ($to === 'shipped') {
                $order->shipped_at = now();
            }if ($to === 'delivered') {
                $order->delivered_at = now();
            }$order->save();
            OrderStatusHistory::create(['order_id' => $order->id, 'from_status' => $from, 'to_status' => $to, 'changed_by_user_id' => Auth::id(), 'note' => $note]);
            if ($to === 'cancelled') {
                $this->restoreStock($order);
            }
        });
        if (filter_var($order->email, FILTER_VALIDATE_EMAIL)) {
            try {
                Notification::route('mail', $order->email)->notify(new OrderStatusUpdated($order->id));
            } catch (\Throwable $e) {
                Log::warning('Order status email could not be queued', ['order_id' => $order->id, 'error' => $e->getMessage()]);
            }
        }
        app(AdminOrderNotificationService::class)->notify($order->fresh(), 'status');
    }

    private function restoreStock(Order $order): void
    {
        $order->load('items');
        foreach ($order->items as $item) {
            $product = Product::find($item->product_id);
            if (! $product || $product->available_for_preorder) {
                continue;
            }$before = $product->stock;
            $product->increment('stock', $item->quantity);
            $product->refresh();
            InventoryMovement::create(['product_id' => $product->id, 'order_id' => $order->id, 'type' => 'cancel_return', 'quantity_delta' => $item->quantity, 'stock_before' => $before, 'stock_after' => $product->stock, 'reference' => $order->order_number, 'created_by_user_id' => Auth::id()]);
        }
    }
}
