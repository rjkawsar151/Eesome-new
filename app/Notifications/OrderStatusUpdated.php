<?php

namespace App\Notifications;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusUpdated extends Notification
{

    public function __construct(public int $orderId) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $order = Order::with('items', 'statusHistories')->findOrFail($this->orderId);
        $statusEnum = OrderStatus::tryFrom($order->order_status);
        $statusLabel = $statusEnum ? $statusEnum->label() : ucfirst($order->order_status);

        $latestHistory = $order->statusHistories->first();
        $isNew = $latestHistory && $latestHistory->from_status === null;

        $statusColor = match ($order->order_status) {
            'awaiting'   => '#d97706',
            'processing' => '#2563eb',
            'confirmed'  => '#0f766e',
            'waiting_for_confirmation' => '#d97706',
            'shipped'    => '#7c3aed',
            'in_transit' => '#0891b2',
            'delivered'  => '#16a34a',
            'cancelled'  => '#dc2626',
            default      => '#6b7280',
        };

        return (new MailMessage)
            ->subject($isNew ? "Order Confirmation #{$order->order_number}" : "Order #{$order->order_number}: {$statusLabel}")
            ->view('mail.order_status_updated', [
                'order'       => $order,
                'statusLabel' => $statusLabel,
                'statusColor' => $statusColor,
                'isNew'       => $isNew,
            ]);
    }
}
