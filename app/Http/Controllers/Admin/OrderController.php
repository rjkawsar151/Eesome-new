<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderStatusService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function __construct(private OrderStatusService $statusService) {}

    public function index(Request $r)
    {
        $q = Order::with('user')->latest();
        if ($r->filled('search')) {
            $s = $r->search;
            $q->where(fn ($x) => $x->where('order_number', 'like', "%$s%")->orWhere('customer_name', 'like', "%$s%")->orWhere('email', 'like', "%$s%")->orWhere('phone', 'like', "%$s%"));
        }foreach (['order_status', 'payment_status', 'payment_method'] as $f) {
            if ($r->filled($f)) {
                $q->where($f, $r->$f);
            }
        }

return view('admin.orders.index', ['orders' => $q->paginate(20)->withQueryString()]);
    }

    public function show(Order $order)
    {
        $order->load(['items.product.images', 'items.variant', 'statusHistories.changedBy', 'paymentTransactions', 'user']);

        return view('admin.orders.show', ['order' => $order, 'allowedNext' => $this->statusService->getAllowedNext($order->order_status)]);
    }

    public function updateStatus(Request $r, Order $order)
    {
        $values = array_column(OrderStatus::cases(), 'value');
        if ($r->filled('tracking_url') && ! preg_match('/^[a-z][a-z0-9+.-]*:\/\//i', $r->tracking_url)) {
            $r->merge(['tracking_url' => 'https://'.$r->tracking_url]);
        }
        $d = $r->validate(['to_status' => ['required', Rule::in($values)], 'note' => 'nullable|string|max:500', 'shipping_provider' => 'nullable|string|max:100', 'tracking_number' => 'nullable|string|max:150', 'tracking_url' => 'nullable|url|max:500', 'estimated_delivery_at' => 'nullable|date']);
        try {
            $this->statusService->transition($order, $d['to_status'], $d['note'] ?? null, $d);

            return back()->with('success', 'Order status updated.');
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['status' => $e->getMessage()]);
        }
    }

    public function updatePayment(Request $r, Order $order)
    {
        $d = $r->validate([
            'payment_status' => 'required|in:unpaid,pending,paid,partially_paid,failed,refunded,partially_refunded',
            'transaction_id' => 'nullable|string|max:150',
        ]);

        try {
            $oldPaymentStatus = $order->payment_status;
            $oldTrx = $order->transaction_id;

            $order->update($d);

            if ($oldPaymentStatus !== $order->payment_status || $oldTrx !== $order->transaction_id) {
                \App\Models\OrderStatusHistory::create([
                    'order_id' => $order->id,
                    'from_status' => $order->order_status,
                    'to_status' => $order->order_status,
                    'changed_by_user_id' => \Illuminate\Support\Facades\Auth::id(),
                    'note' => sprintf(
                        'Payment updated: %s → %s%s',
                        \Illuminate\Support\Str::headline($oldPaymentStatus),
                        \Illuminate\Support\Str::headline($order->payment_status),
                        $order->transaction_id ? " (Trx ID: {$order->transaction_id})" : ''
                    ),
                ]);
            }

            return back()->with('success', 'Payment status updated successfully.');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Order payment update failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['payment_status' => 'Failed to update payment: ' . $e->getMessage()]);
        }
    }
}
