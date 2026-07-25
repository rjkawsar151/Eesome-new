<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Services\CartService;
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
        ]);

        $qty = $data['quantity'] ?? 1;

        if (Auth::check()) {
            $this->cart->addToDbCart(Auth::id(), $data['product_id'], $qty);
        } else {
            $this->cart->addToSessionCart($data['product_id'], $qty);
        }

        return redirect()->route('cart.index')->with('success', 'Item added to cart!');
    }

    public function update(Request $request, int $productId)
    {
        $data = $request->validate([
            'quantity' => 'required|integer|min:0|max:100',
        ]);

        if (Auth::check()) {
            $this->cart->updateDbCart(Auth::id(), $productId, $data['quantity']);
        } else {
            $this->cart->updateSessionCart($productId, $data['quantity']);
        }

        return redirect()->route('cart.index')->with('success', 'Cart updated.');
    }

    public function destroy(int $productId)
    {
        if (Auth::check()) {
            $this->cart->removeFromDbCart(Auth::id(), $productId);
        } else {
            $this->cart->removeFromSessionCart($productId);
        }

        return redirect()->route('cart.index')->with('success', 'Item removed.');
    }
}
