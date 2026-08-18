<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\SiteSettingsRepository;
use Illuminate\Http\Request;

class OrderTrackerController extends Controller
{
    public function index(Request $request)
    {
        $order = null;
        $error = null;
        $fromEmailToken = false;

        // 1. Check for signed URL or email token
        if ($request->hasValidSignature() || $request->filled('token')) {
            $orderId = $request->query('order') ?: $request->query('id');
            $token = $request->query('token');

            if ($orderId) {
                $order = Order::with(['items.product.images', 'items.variant', 'statusHistories.changedBy'])
                    ->where('id', $orderId)
                    ->orWhere('order_number', $orderId)
                    ->first();
            }

            if (!$order && $token) {
                // Direct lookup by order number or HMAC token check
                $order = Order::with(['items.product.images', 'items.variant', 'statusHistories.changedBy'])
                    ->where('order_number', $token)
                    ->orWhere('id', $token)
                    ->first();

                if (!$order) {
                    // Match generated HMAC hash
                    $candidates = Order::latest()->take(300)->get();
                    foreach ($candidates as $c) {
                        $expectedHash = hash_hmac('sha256', $c->id . '|' . $c->order_number, config('app.key'));
                        if (hash_equals($expectedHash, (string) $token)) {
                            $order = $c->load(['items.product.images', 'items.variant', 'statusHistories.changedBy']);
                            break;
                        }
                    }
                }
            }

            if ($order) {
                $fromEmailToken = true;
            } else {
                $error = 'This tracking link is no longer valid or has expired.';
            }
        } elseif ($request->filled('order_number')) {
            $orderNumber = trim((string) $request->query('order_number'));
            $emailOrPhone = trim((string) $request->query('email_or_phone', ''));

            $order = $this->findOrder($orderNumber, $emailOrPhone);

            if (!$order) {
                $error = "We couldn't find that order. Please check your order number and try again.";
            }
        }

        $siteSettings = app(SiteSettingsRepository::class);
        $supportPhone = $siteSettings->get('contact_phone', '01700000000');
        $supportEmail = $siteSettings->get('contact_email', config('mail.from.address', 'support@eesome.com'));

        return view('storefront.orders.track', [
            'order' => $order,
            'error' => $error,
            'fromEmailToken' => $fromEmailToken,
            'supportPhone' => $supportPhone,
            'supportEmail' => $supportEmail,
        ]);
    }

    public function search(Request $request)
    {
        $request->validate([
            'order_number' => 'required|string|max:100',
            'email_or_phone' => 'nullable|string|max:150',
        ]);

        $orderNumber = trim((string) $request->input('order_number'));
        $emailOrPhone = trim((string) $request->input('email_or_phone', ''));

        $order = $this->findOrder($orderNumber, $emailOrPhone);

        $siteSettings = app(SiteSettingsRepository::class);
        $supportPhone = $siteSettings->get('contact_phone', '01700000000');
        $supportEmail = $siteSettings->get('contact_email', config('mail.from.address', 'support@eesome.com'));

        if (!$order) {
            return view('storefront.orders.track', [
                'order' => null,
                'error' => "We couldn't find that order. Please check your order number and try again.",
                'fromEmailToken' => false,
                'searchedOrderNumber' => $orderNumber,
                'searchedEmailOrPhone' => $emailOrPhone,
                'supportPhone' => $supportPhone,
                'supportEmail' => $supportEmail,
            ]);
        }

        return view('storefront.orders.track', [
            'order' => $order,
            'error' => null,
            'fromEmailToken' => false,
            'searchedOrderNumber' => $orderNumber,
            'searchedEmailOrPhone' => $emailOrPhone,
            'supportPhone' => $supportPhone,
            'supportEmail' => $supportEmail,
        ]);
    }

    private function findOrder(string $orderNumber, string $emailOrPhone): ?Order
    {
        $cleanNumber = preg_replace('/[^a-zA-Z0-9-]/', '', $orderNumber);
        $numericId = preg_replace('/\D/', '', $orderNumber);

        $query = Order::with(['items.product.images', 'items.variant', 'statusHistories.changedBy'])
            ->where(function ($q) use ($orderNumber, $cleanNumber, $numericId) {
                $q->where('order_number', $orderNumber)
                  ->orWhere('order_number', $cleanNumber)
                  ->orWhere('order_number', 'EES-' . ltrim($orderNumber, '#'));

                if (!empty($numericId)) {
                    $q->orWhere('id', (int) $numericId);
                }
            });

        if (!empty($emailOrPhone)) {
            $cleanPhone = preg_replace('/\D/', '', $emailOrPhone);
            $query->where(function ($q) use ($emailOrPhone, $cleanPhone) {
                $q->where('email', 'like', "%{$emailOrPhone}%");
                if (!empty($cleanPhone) && strlen($cleanPhone) >= 6) {
                    $q->orWhere('phone', 'like', "%{$cleanPhone}%");
                }
            });
        }

        return $query->first();
    }
}
