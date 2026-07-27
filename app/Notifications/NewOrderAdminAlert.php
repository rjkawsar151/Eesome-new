<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewOrderAdminAlert extends Notification implements ShouldQueue
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

        return (new MailMessage)->subject("New order {$o->order_number}")->line("Customer: {$o->customer_name}")->line("Phone: {$o->phone}")->line('Total: ৳'.number_format((float) $o->total_amount, 0))->line("Payment: {$o->payment_method}")->line("Address: {$o->shipping_address}")->action('Open order', route('admin.orders.show', $o));
    }
}
