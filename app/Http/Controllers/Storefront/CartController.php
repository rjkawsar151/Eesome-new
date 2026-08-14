<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function __construct(private CartService $cart) {}

    public function index()
    {
        if (Auth::check()) {
            $items = $this->cart->getDbCart(Auth::id());
            $isDb = true;
        } else {
            $items = $this->cart->hydrateSessionCart();
            $isDb = false;
        }
        return view('storefront.cart.index', compact('items', 'isDb'));
    }

    public function store(Request $request, \App\Services\MetaCapiService $metaCapi)
    {
        $data = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'quantity'   => 'sometimes|integer|min:1|max:100',
            'variant_id' => 'nullable|integer|exists:product_variants,id',
            'buy_now'    => 'sometimes|boolean',
            'event_id'   => 'nullable|string|max:100',
        ]);

        $qty = $data['quantity'] ?? 1;
        $product = Product::with('activeVariants')->whereKey($data['product_id'])->where('is_active', true)->firstOrFail();
        $variant = isset($data['variant_id']) ? $product->activeVariants->firstWhere('id', (int) $data['variant_id']) : null;
        if (! $variant && $product->has_variants && $product->activeVariants->count() === 1) {
            $variant = $product->activeVariants->first();
        }
        if ($product->has_variants && ! $variant) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Please select an available color.'], 422);
            }
            return back()->withErrors(['variant_id' => 'Please select an available color.']);
        }
        $stock = $variant?->stock ?? $product->stock;
        if (! $product->available_for_preorder && $qty > $stock) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'The requested quantity is not available.'], 422);
            }
            return back()->withErrors(['quantity' => 'The requested quantity is not available.']);
        }

        if (Auth::check()) {
            $this->cart->addToDbCart(Auth::id(), $data['product_id'], $qty, $variant?->id);
        } else {
            $this->cart->addToSessionCart($data['product_id'], $qty, $variant?->id);
        }

        $eventId = $request->input('event_id') ?: (string) \Illuminate\Support\Str::uuid();
        $unitPrice = (float) ($variant ? $variant->effective_price : $product->effective_price);
        $totalVal = round($unitPrice * $qty, 2);
        $contentId = $metaCapi->getCatalogueContentId($product, $variant);

        $metaCapi->trackAddToCart($product, $qty, $totalVal, $request, $eventId, $variant);

        if ($request->boolean('buy_now')) {
            return redirect()->route('checkout.show');
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success'      => true,
                'message'      => 'Successfully added to cart.',
                'cart_count'   => $this->cart->cartCount(),
                'event_id'     => $eventId,
                'content_id'   => $contentId,
                'product_name' => $product->name,
                'value'        => $totalVal,
                'currency'     => 'BDT',
            ]);
        }

        return back()->with('success', 'Successfully added to cart.');
    }

    public function update(Request $request, string $line)
    {
        $data = $request->validate([
            'quantity' => 'required|integer|min:0|max:100',
        ]);

        if (Auth::check()) {
            $this->cart->updateDbCart(Auth::id(), (int) $line, $data['quantity']);
        } else {
            $this->cart->updateSessionCart($line, $data['quantity']);
        }

        return redirect()->route('cart.index')->with('success', 'Cart updated.');
    }

    public function destroy(string $line)
    {
        if (Auth::check()) {
            $this->cart->removeFromDbCart(Auth::id(), (int) $line);
        } else {
            $this->cart->removeFromSessionCart($line);
        }

        return redirect()->route('cart.index')->with('success', 'Item removed.');
    }
}
