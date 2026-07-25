<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CartService
{
    private const SESSION_KEY = 'guest_cart';

    // ─── Guest Cart ───────────────────────────────────────────────

    public function getSessionCart(): array
    {
        return Session::get(self::SESSION_KEY, []);
    }

    public function addToSessionCart(int $productId, int $qty = 1): void
    {
        $cart = $this->getSessionCart();
        $existing = $cart[$productId]['quantity'] ?? 0;
        $cart[$productId] = ['quantity' => $existing + $qty];
        Session::put(self::SESSION_KEY, $cart);
    }

    public function updateSessionCart(int $productId, int $qty): void
    {
        $cart = $this->getSessionCart();
        if ($qty <= 0) {
            unset($cart[$productId]);
        } else {
            $cart[$productId] = ['quantity' => $qty];
        }
        Session::put(self::SESSION_KEY, $cart);
    }

    public function removeFromSessionCart(int $productId): void
    {
        $cart = $this->getSessionCart();
        unset($cart[$productId]);
        Session::put(self::SESSION_KEY, $cart);
    }

    public function clearSessionCart(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    // Hydrate session cart with live product data
    public function hydrateSessionCart(): array
    {
        $raw = $this->getSessionCart();
        if (empty($raw)) return [];

        $products = Product::whereIn('id', array_keys($raw))
            ->where('is_active', true)
            ->get()
            ->keyBy('id');

        $items = [];
        foreach ($raw as $productId => $data) {
            if (!isset($products[$productId])) continue;
            $product = $products[$productId];
            $qty = max(1, (int)$data['quantity']);
            $items[] = [
                'product' => $product,
                'quantity' => $qty,
                'line_total' => bcmul($product->effective_price, (string)$qty, 2),
            ];
        }
        return $items;
    }

    // ─── Auth Cart ────────────────────────────────────────────────

    public function getDbCart(int $userId): \Illuminate\Database\Eloquent\Collection
    {
        return CartItem::with(['product.images'])
            ->where('user_id', $userId)
            ->get();
    }

    public function addToDbCart(int $userId, int $productId, int $qty = 1): void
    {
        $existing = CartItem::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($existing) {
            $existing->increment('quantity', $qty);
        } else {
            CartItem::create([
                'user_id' => $userId,
                'product_id' => $productId,
                'quantity' => $qty,
            ]);
        }
    }

    public function updateDbCart(int $userId, int $productId, int $qty): void
    {
        if ($qty <= 0) {
            CartItem::where('user_id', $userId)->where('product_id', $productId)->delete();
        } else {
            CartItem::where('user_id', $userId)->where('product_id', $productId)
                ->update(['quantity' => $qty]);
        }
    }

    public function removeFromDbCart(int $userId, int $productId): void
    {
        CartItem::where('user_id', $userId)->where('product_id', $productId)->delete();
    }

    public function clearDbCart(int $userId): void
    {
        CartItem::where('user_id', $userId)->delete();
    }

    // ─── Cart Merge on Login ──────────────────────────────────────

    public function mergeSessionCartIntoDb(int $userId): void
    {
        $sessionCart = $this->getSessionCart();
        if (empty($sessionCart)) return;

        DB::transaction(function () use ($userId, $sessionCart) {
            $productIds = array_keys($sessionCart);

            $products = Product::whereIn('id', $productIds)
                ->where('is_active', true)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($sessionCart as $productId => $data) {
                if (!isset($products[$productId])) continue;

                $product = $products[$productId];
                $requestedQty = (int)$data['quantity'];

                $existing = CartItem::where('user_id', $userId)
                    ->where('product_id', $productId)
                    ->first();

                $newQty = ($existing ? $existing->quantity : 0) + $requestedQty;

                // Cap at available stock (0 stock preorder still allowed)
                if (!$product->available_for_preorder && $product->stock > 0) {
                    $newQty = min($newQty, $product->stock);
                }

                if ($newQty > 0) {
                    CartItem::updateOrCreate(
                        ['user_id' => $userId, 'product_id' => $productId],
                        ['quantity' => $newQty]
                    );
                }
            }
        });

        $this->clearSessionCart();
    }

    // ─── Unified cart count ───────────────────────────────────────

    public function cartCount(): int
    {
        if (Auth::check()) {
            return CartItem::where('user_id', Auth::id())->sum('quantity');
        }
        return array_sum(array_column($this->getSessionCart(), 'quantity'));
    }
}
