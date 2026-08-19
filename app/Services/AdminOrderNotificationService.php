<?php
namespace App\Services;
use App\Models\Order;
use App\Models\User;
use App\Notifications\NewOrderAdminAlert;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
class AdminOrderNotificationService
{
    public function recipients(): array
    {
        $configured = collect(config('order_alerts.emails', []));
        $registeredAdmins = User::whereIn('role', ['admin', 'super admin', 'manager', 'content editor'])
            ->whereNotNull('email')->pluck('email');
        return $configured->merge($registeredAdmins)->map(fn ($email) => strtolower(trim((string) $email)))->filter(fn ($email) => filter_var($email, FILTER_VALIDATE_EMAIL))->unique()->values()->all();
    }
    public function notify(Order $order, string $event = 'new'): void
    {
        // Exclude the customer's own email — they already get a separate customer copy
        $customerEmail = strtolower(trim((string) ($order->email ?? '')));

        foreach ($this->recipients() as $email) {
            if ($customerEmail && $email === $customerEmail) {
                continue; // skip — this person is also the customer, already gets a customer email
            }
            try {
                Notification::route('mail', $email)->notify(new NewOrderAdminAlert($order->id, $event));
            } catch (\Throwable $e) {
                Log::warning('Admin order email could not be queued', ['order_id' => $order->id, 'event' => $event, 'email' => $email, 'error' => $e->getMessage()]);
            }
        }
    }
}