<?php
namespace App\Notifications;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;
class NewOrderAdminAlert extends Notification implements ShouldQueue
{
    use Queueable;
    public function __construct(public int $orderId, public string $event = 'new') {}
    public function via(object $notifiable): array { return ['mail']; }
    public function toMail(object $notifiable): MailMessage
    {
        $order = Order::with('items')->findOrFail($this->orderId);
        $isNew = $this->event === 'new';
        $subject = $isNew ? "New order {$order->order_number}" : "Order {$order->order_number} is now ".Str::headline($order->order_status);
        $mail = (new MailMessage)->subject($subject)->line("Customer: {$order->customer_name}")->line("Phone: {$order->phone}")->line('Total: ৳'.number_format((float) $order->total_amount, 0))->line("Payment: ".Str::headline($order->payment_status))->line("Order status: ".Str::headline($order->order_status))->line("Address: {$order->shipping_address}");
        foreach ($order->items as $item) {
            $detail = $item->product_name.' x '.$item->quantity;
            if ($item->display_color) $detail .= ' - Color: '.$item->display_color;
            if ($item->product_sku) $detail .= ' - SKU: '.$item->product_sku;
            $mail->line($detail);
        }
        if ($order->tracking_number) $mail->line("Tracking: {$order->tracking_number}");
        return $mail->action('Open order', route('admin.orders.show', $order));
    }
}