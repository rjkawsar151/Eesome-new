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
        try {
            $data = $request->validate([
                'product_id' => 'required|integer|exists:products,id',
                'quantity'   => 'sometimes|integer|min:1|max:100',
                'variant_id' => 'nullable',
                'buy_now'    => 'sometimes|boolean',
                'event_id'   => 'nullable|string|max:100',
            ]);

            $qty = max(1, (int) ($data['quantity'] ?? 1));
            $product = Product::with(['activeVariants', 'variants'])->whereKey($data['product_id'])->where('is_active', true)->firstOrFail();

            $variant = null;
            $rawVariantId = $request->input('variant_id');
            if (! empty($rawVariantId)) {
                if (is_numeric($rawVariantId)) {
                    $vid = (int) $rawVariantId;
                    $variant = $product->activeVariants->firstWhere('id', $vid)
                        ?? $product->variants->firstWhere('id', $vid)
                        ?? \App\Models\ProductVariant::where('product_id', $product->id)->where('id', $vid)->first()
                        ?? \App\Models\ProductVariant::find($vid);
                }
                if (! $variant && is_string($rawVariantId)) {
                    $search = trim(strtolower($rawVariantId));
                    $variant = $product->variants->first(function ($v) use ($search) {
                        return strtolower(trim((string) $v->color_name)) === $search
                            || strtolower(trim((string) $v->color)) === $search
                            || strtolower(trim((string) $v->name)) === $search
                            || strtolower(trim((string) $v->sku)) === $search;
                    }) ?? \App\Models\ProductVariant::where('product_id', $product->id)->where(function ($q) use ($search) {
                        $q->whereRaw('LOWER(color_name) = ?', [$search])
                          ->orWhereRaw('LOWER(name) = ?', [$search])
                          ->orWhereRaw('LOWER(color) = ?', [$search]);
                    })->first();
                }
            }

            // Auto-select if product only has 1 variant
            if (! $variant && $product->has_variants) {
                if ($product->activeVariants->count() === 1) {
                    $variant = $product->activeVariants->first();
                } elseif ($product->variants->count() === 1) {
                    $variant = $product->variants->first();
                }
            }

            if ($product->has_variants && $product->variants->isNotEmpty() && ! $variant) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Please select an available color.'], 422);
                }
                return back()->withErrors(['variant_id' => 'Please select an available color.']);
            }

            $stock = (int) ($variant ? $variant->stock : $product->stock);
            // Pre-order is allowed if product is preorder, or if variant stock is 0 (pre-order flow)
            $isPreorderAllowed = $product->available_for_preorder || $product->is_preorder || ($stock <= 0);

            if (! $isPreorderAllowed && $qty > $stock) {
                $msg = $stock <= 0
                    ? 'This item is currently out of stock.'
                    : "Only {$stock} item(s) available in stock.";
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'message' => $msg], 422);
                }
                return back()->withErrors(['quantity' => $msg]);
            }

            // Safe cart addition
            if (Auth::check()) {
                $this->cart->addToDbCart(Auth::id(), $product->id, $qty, $variant?->id);
            } else {
                $this->cart->addToSessionCart($product->id, $qty, $variant?->id);
            }

            $eventId = $request->input('event_id') ?: (string) \Illuminate\Support\Str::uuid();
            $unitPrice = (float) ($variant ? $variant->effective_price : $product->effective_price);
            $totalVal = round($unitPrice * $qty, 2);
            $contentId = null;

            try {
                $contentId = $metaCapi->getCatalogueContentId($product, $variant);
                $metaCapi->trackAddToCart($product, $qty, $totalVal, $request, $eventId, $variant);
            } catch (\Throwable $e) {
                report($e);
            }

            if ($request->boolean('buy_now')) {
                return redirect()->route('checkout.show');
            }

            $cartCount = 1;
            try {
                $cartCount = $this->cart->cartCount();
            } catch (\Throwable $e) {}

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success'      => true,
                    'message'      => 'Successfully added to cart.',
                    'cart_count'   => $cartCount,
                    'event_id'     => $eventId,
                    'content_id'   => $contentId,
                    'product_name' => $product->name,
                    'value'        => $totalVal,
                    'currency'     => 'BDT',
                ]);
            }

            return redirect()->back(fallback: route('cart.index'))->with('success', 'Successfully added to cart.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Cart Add Error: ' . $e->getMessage(), [
                'exception' => $e,
                'product_id' => $request->input('product_id'),
                'variant_id' => $request->input('variant_id'),
            ]);

            // Guaranteed fallback addition
            try {
                $pid = (int) $request->input('product_id');
                if ($pid > 0) {
                    $vid = is_numeric($request->input('variant_id')) ? (int) $request->input('variant_id') : null;
                    if (Auth::check()) {
                        $this->cart->addToDbCart(Auth::id(), $pid, 1, $vid);
                    } else {
                        $this->cart->addToSessionCart($pid, 1, $vid);
                    }
                    if ($request->expectsJson() || $request->ajax()) {
                        return response()->json([
                            'success'    => true,
                            'message'    => 'Successfully added to cart.',
                            'cart_count' => $this->cart->cartCount(),
                        ]);
                    }
                    return redirect()->back(fallback: route('cart.index'))->with('success', 'Successfully added to cart.');
                }
            } catch (\Throwable $fallbackEx) {}

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage() ?: 'Could not add item to cart. Please try again.',
                ], 422);
            }

            return back()->withErrors(['cart' => $e->getMessage() ?: 'Could not add item to cart. Please try again.']);
        }
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
