<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\DeliverySetting;
use App\Models\District;
use App\Models\Division;
use App\Models\PaymentMethod;
use App\Models\ShippingMethod;
use App\Services\CartService;
use App\Services\CheckoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CheckoutController extends Controller
{
    public function __construct(private CartService $cartService, private CheckoutService $checkoutService) {}

    public function show()
    {
        $items = Auth::check() ? $this->cartService->getDbCart(Auth::id()) : $this->cartService->hydrateSessionCart();
        if ((is_array($items) && empty($items)) || ($items instanceof \Illuminate\Support\Collection && $items->isEmpty())) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }
        $shippingMethods = ShippingMethod::where('is_active', true)->orderBy('sort_order')->get();
        $paymentMethods = PaymentMethod::where('is_active', true)->orderBy('sort_order')->get();
        if ($shippingMethods->isEmpty() || $paymentMethods->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Checkout is temporarily unavailable. Please contact us.');
        }

        $divisions = Division::where('status', true)->orderBy('sort_order')->orderBy('name')->get();
        $deliverySetting = DeliverySetting::getSettings();

        return view('storefront.checkout.show', compact('items', 'shippingMethods', 'paymentMethods', 'divisions', 'deliverySetting'));
    }

    public function getDistricts(Request $request)
    {
        $divisionId = $request->query('division_id');
        $divisionName = $request->query('division');

        $query = District::where('status', true);
        if ($divisionId) {
            $query->where('division_id', $divisionId);
        } elseif ($divisionName) {
            $query->whereHas('division', fn ($q) => $q->where('name', $divisionName));
        } else {
            return response()->json([]);
        }

        $districts = $query->orderBy('sort_order')->orderBy('name')->get(['id', 'division_id', 'name', 'delivery_charge']);

        return response()->json($districts);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:30',
            'division' => 'nullable|string|max:100',
            'district' => 'required|string|max:100',
            'thana' => 'required|string|max:100',
            'post_office' => 'required|string|max:100',
            'post_code' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'shipping_method' => ['required', 'string', Rule::exists('shipping_methods', 'code')->where('is_active', true)],
            'payment_method' => ['required', 'string', Rule::exists('payment_methods', 'code')->where('is_active', true)],
            'transaction_id' => 'nullable|string|max:100',
            'coupon_code' => 'nullable|string|max:100',
        ]);

        $paymentMethod = PaymentMethod::where('code', $data['payment_method'])->where('is_active', true)->firstOrFail();
        if ($paymentMethod->requires_transaction_id) {
            $request->validate([
                'transaction_id' => 'required|string|min:3|max:100',
            ], [
                'transaction_id.required' => 'Transaction ID is required for ' . $paymentMethod->name . '.',
                'transaction_id.min' => 'Please enter a valid Transaction ID.',
            ]);
            $data['transaction_id'] = trim((string) $request->input('transaction_id'));
        } else {
            $data['transaction_id'] = $request->filled('transaction_id') ? trim((string) $request->input('transaction_id')) : null;
        }

        $division = null;
        $district = null;
        if (! empty($data['division'])) {
            $division = Division::where('name', $data['division'])->where('status', true)->first();
            if ($division) {
                $district = District::where('name', $data['district'])->where('division_id', $division->id)->where('status', true)->first();
            }
        }
        if (! $district) {
            $district = District::where('name', $data['district'])->where('status', true)->first();
            if ($district && ! $division) {
                $division = $district->division ?? Division::find($district->division_id);
            }
        }

        $data['division_id'] = $division?->id;
        $data['district_id'] = $district?->id;
        $data['division'] = $division?->name ?? ($data['division'] ?? $data['district']);

        $shippingMethod = ShippingMethod::where('code', $data['shipping_method'])->where('is_active', true)->firstOrFail();
        if ($data['payment_method'] === 'COD' && ! $shippingMethod->cod_available) {
            return back()->withInput()->withErrors(['payment_method' => 'Cash on delivery is not available for this delivery method.']);
        }
        if (Auth::check()) {
            $rawItems = $this->cartService->getDbCart(Auth::id());
            $cartMap = $rawItems->map(fn ($item) => ['product_id' => $item->product_id, 'variant_id' => $item->variant_id, 'quantity' => $item->quantity])->all();
        } else {
            $rawItems = $this->cartService->hydrateSessionCart();
            $cartMap = collect($rawItems)->map(fn ($item) => ['product_id' => $item['product']->id, 'variant_id' => $item['variant']?->id, 'quantity' => $item['quantity']])->all();
        }
        if (empty($cartMap)) {
            return back()->with('error', 'Cart is empty.');
        }

        $data['user_id'] = Auth::id();
        try {
            $order = $this->checkoutService->placeOrder($data, $cartMap, $data['coupon_code'] ?? null);
            if (! Auth::check()) {
                $this->cartService->clearSessionCart();
            }

            try {
                app(\App\Services\MetaCapiService::class)->trackPurchase($order, $request, 'order_' . $order->order_number);
            } catch (\Throwable $capiEx) {
                report($capiEx);
            }

            return redirect()->route('checkout.success', $order->order_number);
        } catch (\RuntimeException $e) {
            return back()->withInput()->withErrors(['checkout' => $e->getMessage()]);
        } catch (\Throwable $e) {
            report($e);
            return back()->withInput()->withErrors(['checkout' => 'We could not place your order right now. Please try again or contact support.']);
        }
    }

    public function success(string $orderNumber)
    {
        $order = \App\Models\Order::with('items')->where('order_number', $orderNumber)->firstOrFail();

        return view('storefront.checkout.success', compact('order'));
    }
}
