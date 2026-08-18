<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\InventoryMovement;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutService
{
    public function calculateShipping(float $subtotal, ?string $shippingMethod = null): float
    {
        return app(ShippingCalculator::class)->calculate($subtotal, $shippingMethod);

    }

    public function placeOrder(array $customerData, array $cartLines, ?string $couponCode = null): Order
    {
        if ($cartLines && ! isset($cartLines[0]['product_id'])) {
            $cartLines = collect($cartLines)->map(fn ($quantity, $productId) => ['product_id' => $productId, 'variant_id' => null, 'quantity' => $quantity])->values()->all();
        }
        $order = DB::transaction(function () use ($customerData, $cartLines, $couponCode) {
            // 1. Sort product IDs ascending to prevent deadlocks
            $productIds = array_values(array_unique(array_column($cartLines, 'product_id')));

            // 2. Lock products
            $products = Product::with('variants')->whereIn('id', $productIds)
                ->where('is_active', true)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            // 3. Validate each cart item
            $orderLines = [];
            $subtotal = 0.0;

            foreach ($cartLines as $cartLine) {
                $productId = (int) $cartLine['product_id'];
                $requestedQty = (int) $cartLine['quantity'];
                if (! isset($products[$productId])) {
                    throw new \RuntimeException("Product #{$productId} is no longer available.");
                }

                $product = $products[$productId];
                $qty = (int) $requestedQty;
                $variant = empty($cartLine['variant_id']) ? null : $product->variants->firstWhere('id', (int) $cartLine['variant_id']);
                if ($product->has_variants and ! $variant) throw new \RuntimeException('A selected color is no longer available.');
                if ($variant and ! $variant->is_active) throw new \RuntimeException('A selected color is inactive.');
                $availableStock = $variant ? $variant->stock : $product->stock;

                if (! $product->available_for_preorder and $availableStock < $qty) {
                    throw new \RuntimeException("'{$product->name}' has insufficient stock.");
                }

                $unitPrice = (float) ($variant ? $variant->effective_price : $product->effective_price);
                $lineTotal = round($unitPrice * $qty, 2);
                $subtotal += $lineTotal;

                $orderLines[] = [
                    'product' => $product,
                    'variant' => $variant,
                    'qty' => $qty,
                    'unit_price' => $unitPrice,
                    'line_total' => $lineTotal,
                ];
            }

            // 4. Coupon validation
            $discount = 0.0;
            $coupon = null;
            if ($couponCode) {
                $coupon = Coupon::where('code', strtoupper($couponCode))
                    ->lockForUpdate()
                    ->first();

                if (! $coupon || ! $coupon->isValidForSubtotal($subtotal)) {
                    throw new \RuntimeException("Coupon '{$couponCode}' is invalid or expired.");
                }
                $discount = round($coupon->calculateDiscount($subtotal), 2);
            }

            // 5. Shipping
            $shipping = $this->calculateShipping($subtotal, $customerData['shipping_method'] ?? null);
            $total = round($subtotal - $discount + $shipping, 2);

            // 6. Create order
            $orderNumber = 'EES-'.strtoupper(Str::random(8));
            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => $customerData['user_id'] ?? null,
                'customer_name' => $customerData['name'],
                'email' => $customerData['email'],
                'phone' => $customerData['phone'],
                'district' => $customerData['district'] ?? null,
                'thana' => $customerData['thana'] ?? null,
                'post_office' => $customerData['post_office'] ?? null,
                'post_code' => $customerData['post_code'] ?? null,
                'shipping_address' => $customerData['address'],
                'shipping_method' => $customerData['shipping_method'] ?? null,
                'subtotal_amount' => (string) $subtotal,
                'discount_amount' => (string) $discount,
                'shipping_charge' => (string) $shipping,
                'payment_fee' => '0',
                'total_amount' => (string) $total,
                'coupon_code' => $coupon ? $coupon->code : null,
                'coupon_id' => $coupon ? $coupon->id : null,
                'payment_method' => $customerData['payment_method'] ?? 'COD',
                'payment_status' => 'pending',
                'order_status' => 'awaiting',
                'placed_from' => 'web',
            ]);

            // 7. Create order items (snapshots)
            foreach ($orderLines as $line) {
                $p = $line['product'];
                $variant = $line['variant'];
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $p->id,
                    'variant_id' => $variant?->id,
                    'product_name' => $p->name,
                    'product_sku' => $variant?->sku ?? $p->sku ?? 'LEGACY-'.$p->id,
                    'selected_color_name' => $variant?->color_name ?? $variant?->color,
                    'selected_color_code' => $variant?->color_code,
                    'product_image' => $variant?->image ?? $p->image,
                    'price' => (string) $line['unit_price'],
                    'quantity' => $line['qty'],
                    'line_total' => (string) $line['line_total'],
                    'discount_amount' => '0',
                ]);

                // 8. Decrement stock
                $inventory = $variant ?: $p;
                if ($inventory->stock > 0 || ! $p->available_for_preorder) {
                    $stockBefore = $inventory->stock;
                    $inventory->decrement('stock', $line['qty']);
                    $inventory->refresh();

                    InventoryMovement::create([
                        'product_id' => $p->id,
                        'variant_id' => $variant?->id,
                        'order_id' => $order->id,
                        'type' => 'sale',
                        'quantity_delta' => -$line['qty'],
                        'stock_before' => $stockBefore,
                        'stock_after' => $inventory->stock,
                        'reference' => $order->order_number,
                    ]);
                }
            }

            // 9. Increment coupon usage
            if ($coupon) {
                $coupon->increment('used_count');
            }

            // 10. Initial status history
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'from_status' => null,
                'to_status' => 'awaiting',
                'note' => 'Order placed via web.',
            ]);

            return $order;

        });

        // 11. Post-commit: clear cart
        if (! empty($customerData['user_id'])) {
            CartItem::where('user_id', $customerData['user_id'])->delete();
        }

        if (filter_var($order->email, FILTER_VALIDATE_EMAIL)) {
            try {
                \Illuminate\Support\Facades\Notification::route('mail', $order->email)
                    ->notify(new \App\Notifications\OrderStatusUpdated($order->id));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Order confirmation could not be queued', ['order_id' => $order->id, 'error' => $e->getMessage()]);
            }
        }
        app(AdminOrderNotificationService::class)->notify($order, 'new');

        return $order;
    }
}
