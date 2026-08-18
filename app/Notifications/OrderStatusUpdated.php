<?php

namespace App\Notifications;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Services\SiteSettingsRepository;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class OrderStatusUpdated extends Notification
{
    public function __construct(public int $orderId) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $order = Order::with(['items.product.images', 'items.variant', 'statusHistories'])->findOrFail($this->orderId);

        $statusKey = strtolower($order->order_status);
        $statusEnum = OrderStatus::tryFrom($statusKey);
        $statusLabel = $statusEnum ? $statusEnum->label() : \Illuminate\Support\Str::headline($order->order_status);

        $latestHistory = $order->statusHistories->first();
        $isNew = $latestHistory && $latestHistory->from_status === null;

        // Dynamic status badge styling matching Eesome branding
        $badgeBg = match ($statusKey) {
            'awaiting', 'pending', 'waiting_for_confirmation' => '#FEF3C7',
            'processing' => '#FFF6FA',
            'confirmed'  => '#FCECF4',
            'shipped', 'in_transit' => '#F3E8FF',
            'delivered'  => '#DCFCE7',
            'cancelled'  => '#FFF1F1',
            'refunded', 'partially_refunded' => '#FFF1F1',
            'on_hold'    => '#FEF3C7',
            default      => '#FFF6FA',
        };

        $badgeText = match ($statusKey) {
            'awaiting', 'pending', 'waiting_for_confirmation' => '#92400E',
            'processing' => '#B54A7B',
            'confirmed'  => '#6F2F50',
            'shipped', 'in_transit' => '#6B21A8',
            'delivered'  => '#15803D',
            'cancelled'  => '#D92D20',
            'refunded', 'partially_refunded' => '#842029',
            'on_hold'    => '#92400E',
            default      => '#6F2F50',
        };

        // Dynamic status messages and titles
        $statusTitle = match ($statusKey) {
            'confirmed'  => 'Your order is confirmed',
            'processing' => 'We\'re preparing your order',
            'shipped', 'in_transit' => 'Your order is on the way',
            'delivered'  => 'Your order has been delivered',
            'cancelled'  => 'Order Cancellation Notice',
            'on_hold'    => 'Your order is on hold',
            'refunded', 'partially_refunded' => 'Refund Processed',
            default      => "Order Status Update: {$statusLabel}",
        };

        $statusMessage = match ($statusKey) {
            'confirmed'  => 'We\'ve received your order and will begin preparing it shortly.',
            'processing' => 'Your order is currently being prepared for shipment.',
            'shipped', 'in_transit' => 'Your order has been shipped and is on its way to you.',
            'delivered'  => 'Your order has been successfully delivered. We hope you love your purchase!',
            'cancelled'  => 'We regret to inform you that your order has been cancelled.',
            'on_hold'    => 'Your order is currently on hold. We will update you as soon as processing resumes.',
            'refunded', 'partially_refunded' => 'A refund has been processed for your order.',
            default      => "Your order status has been updated to {$statusLabel}.",
        };

        // Generate signed 1-click tracking URL for the email button
        $token = hash_hmac('sha256', $order->id . '|' . $order->order_number, config('app.key'));
        $trackingUrl = URL::signedRoute('orders.track', [
            'order' => $order->id,
            'token' => $token,
        ]);

        $siteSettings = app(SiteSettingsRepository::class);
        $supportPhone = $siteSettings->get('contact_phone', '01700000000');
        $supportEmail = $siteSettings->get('contact_email', config('mail.from.address', 'support@eesome.com'));
        $logoPath = $siteSettings->get('logo_path');
        $logoUrl = $logoPath ? asset('storage/'.$logoPath) : asset('favicon.svg');

        $subject = $isNew
            ? "Order Confirmation #{$order->order_number}"
            : ($statusKey === 'cancelled'
                ? "Cancellation Notice for Order #{$order->order_number}"
                : "Order #{$order->order_number}: {$statusLabel}");

        return (new MailMessage)
            ->subject($subject)
            ->view('mail.order_status_updated', [
                'order'           => $order,
                'statusLabel'     => $statusLabel,
                'statusTitle'     => $statusTitle,
                'statusMessage'   => $statusMessage,
                'badgeBg'         => $badgeBg,
                'badgeText'       => $badgeText,
                'isCancelled'     => $statusKey === 'cancelled',
                'trackingUrl'     => $trackingUrl,
                'supportPhone'    => $supportPhone,
                'supportPhoneLink'=> preg_replace('/\D/', '', $supportPhone),
                'supportEmail'    => $supportEmail,
                'logoUrl'         => $logoUrl,
                'websiteUrl'      => url('/'),
                'websiteDisplay'  => preg_replace('#^https?://#', '', url('/')),
                'isNew'           => $isNew,
            ]);
    }
}
