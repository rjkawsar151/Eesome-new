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

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'quantity'   => 'sometimes|integer|min:1|max:100',
            'variant_id' => 'nullable|integer|exists:product_variants,id',
            'buy_now'    => 'sometimes|boolean',
        ]);

        $qty = $data['quantity'] ?? 1;
        $product = Product::with('activeVariants')->whereKey($data['product_id'])->where('is_active', true)->firstOrFail();
        $variant = isset($data['variant_id']) ? $product->activeVariants->firstWhere('id', (int) $data['variant_id']) : null;
        if ($product->has_variants && ! $variant) return back()->withErrors(['variant_id' => 'Please select an available color.']);
        $stock = $variant?->stock ?? $product->stock;
        if (! $product->available_for_preorder && $qty > $stock) return back()->withErrors(['quantity' => 'The requested quantity is not available.']);

        if (Auth::check()) {
            $this->cart->addToDbCart(Auth::id(), $data['product_id'], $qty, $variant?->id);
        } else {
            $this->cart->addToSessionCart($data['product_id'], $qty, $variant?->id);
        }

        if ($request->boolean('buy_now')) {
            return redirect()->route('checkout.show');
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
