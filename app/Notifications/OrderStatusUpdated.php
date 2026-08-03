<?php

namespace App\Notifications;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $orderId) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $o = Order::with('items')->findOrFail($this->orderId);
        $status = OrderStatus::from($o->order_status)->label();
        $mail = (new MailMessage)->subject("Order {$o->order_number}: {$status}")->greeting("Hello {$o->customer_name},")->line("Your order is now {$status}.")->line('Order total: ৳'.number_format((float) $o->total_amount, 0));
        foreach ($o->items as $item) {
            $detail = $item->product_name.' x '.$item->quantity;
            if ($item->display_color) $detail .= ' - Color: '.$item->display_color;
            if ($item->product_sku) $detail .= ' - SKU: '.$item->product_sku;
            $mail->line($detail);
        }
        if ($o->tracking_number) {
            $mail->line("Tracking: {$o->tracking_number}");
        }if ($o->tracking_url) {
            $mail->action('Track shipment', $o->tracking_url);
        }

return $mail->line('Thank you for shopping with us.');
    }
}
