<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use App\Services\CheckoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function __construct(
        private CartService $cartService,
        private CheckoutService $checkoutService
    ) {}

    public function show()
    {
        if (Auth::check()) {
            $items = $this->cartService->getDbCart(Auth::id());
        } else {
            $items = $this->cartService->hydrateSessionCart();
        }

        if ((is_array($items) && empty($items)) || ($items instanceof \Illuminate\Support\Collection && $items->isEmpty())) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        return view('storefront.checkout.show', compact('items'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|max:255',
            'phone'          => 'required|string|max:30',
            'address'        => 'required|string|max:500',
            'payment_method' => 'required|in:COD,bKash',
            'coupon_code'    => 'nullable|string|max:100',
        ]);

        // Build cart product IDs map
        if (Auth::check()) {
            $rawItems = $this->cartService->getDbCart(Auth::id());
            $cartMap = $rawItems->pluck('quantity', 'product_id')->toArray();
        } else {
            $rawItems = $this->cartService->hydrateSessionCart();
            $cartMap = collect($rawItems)->mapWithKeys(fn($i) => [$i['product']->id => $i['quantity']])->toArray();
        }

        if (empty($cartMap)) {
            return back()->with('error', 'Cart is empty.');
        }

        $data['user_id'] = Auth::id();

        try {
            $order = $this->checkoutService->placeOrder($data, $cartMap, $data['coupon_code'] ?? null);

            if (!Auth::check()) {
                $this->cartService->clearSessionCart();
            }

            return redirect()->route('checkout.success', $order->order_number);
        } catch (\RuntimeException $e) {
            return back()->withErrors(['checkout' => $e->getMessage()]);
        }
    }

    public function success(string $orderNumber)
    {
        $order = \App\Models\Order::with('items')
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        return view('storefront.checkout.success', compact('order'));
    }
}
