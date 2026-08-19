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

    private function lineKey(int $productId, ?int $variantId = null): string { return $productId.':'.($variantId ?: 0); }

    public function addToSessionCart(int $productId, int $qty = 1, ?int $variantId = null): void
    {
        $cart = $this->getSessionCart();
        $key = $this->lineKey($productId, $variantId);
        $existing = $cart[$key]['quantity'] ?? 0;
        $cart[$key] = ['product_id' => $productId, 'variant_id' => $variantId, 'quantity' => $existing + $qty];
        Session::put(self::SESSION_KEY, $cart);
    }

    public function updateSessionCart(string $key, int $qty): void
    {
        $cart = $this->getSessionCart();
        if ($qty <= 0) {
            unset($cart[$key]);
        } else {
            if (isset($cart[$key])) $cart[$key]['quantity'] = $qty;
        }
        Session::put(self::SESSION_KEY, $cart);
    }

    public function removeFromSessionCart(string $key): void
    {
        $cart = $this->getSessionCart();
        unset($cart[$key]);
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

        $productIds = collect($raw)->map(fn ($item, $key) => $item['product_id'] ?? (int) $key)->all();
        $products = Product::with(['images', 'activeVariants', 'variants'])->whereIn('id', $productIds)
            ->where('is_active', true)
            ->get()
            ->keyBy('id');

        $items = [];
        foreach ($raw as $key => $data) {
            $productId = (int) ($data['product_id'] ?? $key);
            if (!isset($products[$productId])) continue;
            $product = $products[$productId];
            
            $variant = ! empty($data['variant_id'])
                ? ($product->activeVariants->firstWhere('id', (int) $data['variant_id']) ?? $product->variants->firstWhere('id', (int) $data['variant_id']))
                : null;

            if ($product->has_variants && ! $variant) {
                if ($product->activeVariants->count() === 1) {
                    $variant = $product->activeVariants->first();
                } elseif ($product->variants->count() === 1) {
                    $variant = $product->variants->first();
                } else {
                    continue;
                }
            }

            $qty = max(1, (int)$data['quantity']);
            $unitPrice = (string) ($variant?->effective_price ?? $product->effective_price);
            $items[] = [
                'product' => $product,
                'quantity' => $qty,
                'variant' => $variant,
                'key' => (string) $key,
                'unit_price' => $unitPrice,
                'line_total' => bcmul($unitPrice, (string)$qty, 2),
            ];
        }
        return $items;
    }

    // ─── Auth Cart ────────────────────────────────────────────────

    public function getDbCart(int $userId): \Illuminate\Database\Eloquent\Collection
    {
        return CartItem::with(['product.images', 'product.variants', 'productVariant'])
            ->where('user_id', $userId)
            ->get();
    }

    public function addToDbCart(int $userId, int $productId, int $qty = 1, ?int $variantId = null): void
    {
        try {
            $existing = CartItem::where('user_id', $userId)
                ->where('product_id', $productId)
                ->where('variant_id', $variantId)
                ->first();

            if ($existing) {
                $existing->increment('quantity', $qty);
            } else {
                try {
                    CartItem::create([
                        'user_id'    => $userId,
                        'product_id' => $productId,
                        'variant_id' => $variantId,
                        'quantity'   => $qty,
                    ]);
                } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
                    // Race condition: another request inserted in the meantime — increment instead
                    CartItem::where('user_id', $userId)
                        ->where('product_id', $productId)
                        ->where('variant_id', $variantId)
                        ->increment('quantity', $qty);
                }
            }
        } catch (\Throwable $e) {
            // Guarantee storage in session cart as reliable fallback
            $this->addToSessionCart($productId, $qty, $variantId);
        }
    }

    public function updateDbCart(int $userId, int $lineId, int $qty): void
    {
        if ($qty <= 0) {
            CartItem::where('user_id', $userId)->whereKey($lineId)->delete();
        } else {
            CartItem::where('user_id', $userId)->whereKey($lineId)
                ->update(['quantity' => $qty]);
        }
    }

    public function removeFromDbCart(int $userId, int $lineId): void
    {
        CartItem::where('user_id', $userId)->whereKey($lineId)->delete();
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
            $productIds = collect($sessionCart)->map(fn ($item, $key) => $item['product_id'] ?? (int) $key)->all();

            $products = Product::whereIn('id', $productIds)
                ->where('is_active', true)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($sessionCart as $key => $data) {
                $productId = (int) ($data['product_id'] ?? $key);
                $variantId = empty($data['variant_id']) ? null : (int) $data['variant_id'];
                if (!isset($products[$productId])) continue;

                $product = $products[$productId];
                $requestedQty = (int)$data['quantity'];

                $existing = CartItem::where('user_id', $userId)
                    ->where('product_id', $productId)
                    ->where('variant_id', $variantId)
                    ->first();

                $newQty = ($existing ? $existing->quantity : 0) + $requestedQty;

                // Cap at available stock (0 stock preorder still allowed)
                if (!$product->available_for_preorder && $product->stock > 0) {
                    $newQty = min($newQty, $product->stock);
                }

                if ($newQty > 0) {
                    CartItem::updateOrCreate(
                        ['user_id' => $userId, 'product_id' => $productId, 'variant_id' => $variantId],
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
        try {
            if (Auth::check()) {
                $dbCount = (int) CartItem::where('user_id', Auth::id())->sum('quantity');
                if ($dbCount > 0) return $dbCount;
            }
        } catch (\Throwable $e) {}
        return (int) array_sum(array_column($this->getSessionCart(), 'quantity'));
    }
}
