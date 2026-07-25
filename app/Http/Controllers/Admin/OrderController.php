<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderStatusService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(private OrderStatusService $statusService) {}

    public function index(Request $request)
    {
        $query = Order::with('user')->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('order_number', 'like', "%$s%")
                  ->orWhere('customer_name', 'like', "%$s%")
                  ->orWhere('email', 'like', "%$s%")
                  ->orWhere('phone', 'like', "%$s%");
            });
        }
        if ($request->filled('order_status'))  $query->where('order_status', $request->order_status);
        if ($request->filled('payment_status')) $query->where('payment_status', $request->payment_status);
        if ($request->filled('payment_method')) $query->where('payment_method', $request->payment_method);

        $orders = $query->paginate(20)->withQueryString();
        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['items', 'statusHistories', 'paymentTransactions', 'user']);
        $allowedNext = $this->statusService->getAllowedNext($order->order_status);
        return view('admin.orders.show', compact('order', 'allowedNext'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $data = $request->validate([
            'to_status' => 'required|string',
            'note'      => 'nullable|string|max:500',
        ]);

        try {
            $this->statusService->transition($order, $data['to_status'], $data['note'] ?? null);
            return back()->with('success', "Order status updated to {$data['to_status']}.");
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['status' => $e->getMessage()]);
        }
    }
}
