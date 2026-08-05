<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class NewOrderAdminAlert extends Notification
{

    public function __construct(public int $orderId, public string $event = 'new') {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $order = Order::with('items')->findOrFail($this->orderId);
        $isNew = $this->event === 'new';

        $statusLabel = Str::headline($order->order_status);
        $subject = $isNew
            ? "New Order #{$order->order_number} � {$order->customer_name}"
            : "Order #{$order->order_number} is now {$statusLabel}";

        return (new MailMessage)
            ->subject($subject)
            ->view('mail.admin_order_alert', [
                'order'       => $order,
                'isNew'       => $isNew,
                'statusLabel' => $statusLabel,
                'adminUrl'    => route('admin.orders.show', $order),
            ]);
    }
}
