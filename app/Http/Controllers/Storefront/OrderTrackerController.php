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
        $orderNumber = trim((string) $request->query('order_number', $request->query('query', '')));
        $phone = trim((string) $request->query('phone', $request->query('email_or_phone', '')));

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

            if (! $order && $token) {
                $order = Order::with(['items.product.images', 'items.variant', 'statusHistories.changedBy'])
                    ->where('order_number', $token)
                    ->orWhere('id', $token)
                    ->first();

                if (! $order) {
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
        } elseif (! empty($orderNumber) || ! empty($phone)) {
            $result = $this->searchOrderByOrderNumberAndPhone($orderNumber, $phone);
            $order = $result['order'];
            $error = $result['error'];
        }

        $siteSettings = app(SiteSettingsRepository::class);
        $supportPhone = $siteSettings->get('contact_phone', '01700000000');
        $supportEmail = $siteSettings->get('contact_email', config('mail.from.address', 'support@eesome.com'));

        return view('storefront.orders.track', [
            'order' => $order,
            'error' => $error,
            'fromEmailToken' => $fromEmailToken,
            'searchedOrderNumber' => $orderNumber,
            'searchedPhone' => $phone,
            'supportPhone' => $supportPhone,
            'supportEmail' => $supportEmail,
        ]);
    }

    public function search(Request $request)
    {
        $orderNumber = trim((string) $request->input('order_number', $request->input('search_term', '')));
        $phone = trim((string) $request->input('phone', $request->input('email_or_phone', '')));

        $result = $this->searchOrderByOrderNumberAndPhone($orderNumber, $phone);

        $siteSettings = app(SiteSettingsRepository::class);
        $supportPhone = $siteSettings->get('contact_phone', '01700000000');
        $supportEmail = $siteSettings->get('contact_email', config('mail.from.address', 'support@eesome.com'));

        return view('storefront.orders.track', [
            'order' => $result['order'],
            'error' => $result['error'],
            'fromEmailToken' => false,
            'searchedOrderNumber' => $orderNumber,
            'searchedPhone' => $phone,
            'supportPhone' => $supportPhone,
            'supportEmail' => $supportEmail,
        ]);
    }

    private function searchOrderByOrderNumberAndPhone(string $orderNumber, string $phone = ''): array
    {
        if (empty($orderNumber) && empty($phone)) {
            return [
                'order' => null,
                'error' => "We couldn't find that order. Please check the details and try again.",
            ];
        }

        $cleanOrderNum = trim($orderNumber);
        $numericId = (int) preg_replace('/\D/', '', $cleanOrderNum);
        $contact = trim($phone);
        $cleanPhoneDigits = preg_replace('/\D/', '', $contact);

        $query = Order::with(['items.product.images', 'items.variant', 'statusHistories.changedBy']);

        if (! empty($cleanOrderNum)) {
            $query->where(function ($q) use ($cleanOrderNum, $numericId) {
                $q->where('order_number', $cleanOrderNum)
                  ->orWhere('order_number', 'EES-' . ltrim($cleanOrderNum, '#'))
                  ->orWhere('order_number', 'ES-' . ltrim($cleanOrderNum, '#'));

                if ($numericId > 0) {
                    $q->orWhere('id', $numericId);
                }
            });
        }

        if (! empty($contact)) {
            $query->where(function ($q) use ($contact, $cleanPhoneDigits) {
                $q->where('email', $contact)
                  ->orWhere('phone', $contact);

                if (strlen($cleanPhoneDigits) >= 6) {
                    $q->orWhere('phone', 'like', "%{$cleanPhoneDigits}%");
                }
            });
        }

        $order = $query->first();

        if (! $order) {
            return [
                'order' => null,
                'error' => "We couldn't find that order. Please check the details and try again.",
            ];
        }

        return [
            'order' => $order,
            'error' => null,
        ];
    }
}
