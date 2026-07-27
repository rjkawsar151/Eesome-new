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

    public function placeOrder(array $customerData, array $cartProductIds, ?string $couponCode = null): Order
    {
        $order = DB::transaction(function () use ($customerData, $cartProductIds, $couponCode) {
            // 1. Sort product IDs ascending to prevent deadlocks
            ksort($cartProductIds);

            // 2. Lock products
            $products = Product::whereIn('id', array_keys($cartProductIds))
                ->where('is_active', true)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            // 3. Validate each cart item
            $orderLines = [];
            $subtotal = 0.0;

            foreach ($cartProductIds as $productId => $requestedQty) {
                if (! isset($products[$productId])) {
                    throw new \RuntimeException("Product #{$productId} is no longer available.");
                }

                $product = $products[$productId];
                $qty = (int) $requestedQty;

                if (! $product->available_for_preorder && $product->stock < $qty) {
                    throw new \RuntimeException("'{$product->name}' has insufficient stock.");
                }

                $unitPrice = (float) $product->effective_price;
                $lineTotal = round($unitPrice * $qty, 2);
                $subtotal += $lineTotal;

                $orderLines[] = [
                    'product' => $product,
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
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $p->id,
                    'product_name' => $p->name,
                    'product_sku' => $p->sku ?? 'LEGACY-'.$p->id,
                    'product_image' => $p->image,
                    'price' => (string) $line['unit_price'],
                    'quantity' => $line['qty'],
                    'line_total' => (string) $line['line_total'],
                    'discount_amount' => '0',
                ]);

                // 8. Decrement stock
                if (! $p->available_for_preorder) {
                    $stockBefore = $p->stock;
                    $p->decrement('stock', $line['qty']);
                    $p->refresh();

                    InventoryMovement::create([
                        'product_id' => $p->id,
                        'order_id' => $order->id,
                        'type' => 'sale',
                        'quantity_delta' => -$line['qty'],
                        'stock_before' => $stockBefore,
                        'stock_after' => $p->stock,
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
        foreach (config('order_alerts.emails', []) as $email) {
            try {
                \Illuminate\Support\Facades\Notification::route('mail', $email)
                    ->notify(new \App\Notifications\NewOrderAdminAlert($order->id));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Admin order alert could not be queued', ['order_id' => $order->id, 'email' => $email]);
            }
        }

        return $order;
    }
}
