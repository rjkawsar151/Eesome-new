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
        $token = trim((string) $request->query('token', ''));

        // 1. Check for signed URL or valid email token
        if ($request->hasValidSignature() || ! empty($token)) {
            $orderId = $request->query('order') ?: ($request->query('id') ?: $orderNumber);

            if ($request->hasValidSignature() && $orderId) {
                $order = Order::with(['items.product.images', 'items.variant', 'statusHistories.changedBy'])
                    ->where('id', $orderId)
                    ->orWhere('order_number', $orderId)
                    ->first();
                if ($order) {
                    $fromEmailToken = true;
                }
            } elseif (! empty($token)) {
                // If order identifier is provided, directly verify token for that order
                if ($orderId) {
                    $candidate = Order::with(['items.product.images', 'items.variant', 'statusHistories.changedBy'])
                        ->where('id', $orderId)
                        ->orWhere('order_number', $orderId)
                        ->first();
                    if ($candidate) {
                        $expectedHash = hash_hmac('sha256', $candidate->id . '|' . $candidate->order_number, config('app.key'));
                        if (hash_equals($expectedHash, $token)) {
                            $order = $candidate;
                            $fromEmailToken = true;
                        }
                    }
                }

                // If not found yet, search recent candidate orders for HMAC match
                if (! $order) {
                    $candidates = Order::latest('id')->take(300)->get();
                    foreach ($candidates as $c) {
                        $expectedHash = hash_hmac('sha256', $c->id . '|' . $c->order_number, config('app.key'));
                        if (hash_equals($expectedHash, $token)) {
                            $order = $c->load(['items.product.images', 'items.variant', 'statusHistories.changedBy']);
                            $fromEmailToken = true;
                            break;
                        }
                    }
                }
            }

            if (! $order) {
                $error = 'This tracking link is invalid or has expired.';
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
        $cleanOrderNum = trim($orderNumber);
        $contact = trim($phone);

        // Security: require BOTH order number and phone to prevent sequential ID enumeration
        if (empty($cleanOrderNum) || empty($contact)) {
            return [
                'order' => null,
                'error' => "We couldn't find that order. Please enter both your Order Code and Phone Number to track your order.",
            ];
        }

        $numericId = (int) preg_replace('/\D/', '', $cleanOrderNum);
        $cleanPhoneDigits = preg_replace('/\D/', '', $contact);

        $query = Order::with(['items.product.images', 'items.variant', 'statusHistories.changedBy']);

        $query->where(function ($q) use ($cleanOrderNum, $numericId) {
            $q->where('order_number', $cleanOrderNum)
              ->orWhere('order_number', 'EES-' . ltrim($cleanOrderNum, '#'))
              ->orWhere('order_number', 'ES-' . ltrim($cleanOrderNum, '#'));

            if ($numericId > 0) {
                $q->orWhere('id', $numericId);
            }
        });

        $query->where(function ($q) use ($contact, $cleanPhoneDigits) {
            $q->where('email', $contact)
              ->orWhere('phone', $contact);

            if (strlen($cleanPhoneDigits) >= 6) {
                $q->orWhere('phone', 'like', "%{$cleanPhoneDigits}%");
            }
        });

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

