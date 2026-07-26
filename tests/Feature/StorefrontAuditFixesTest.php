<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CartItem;
use App\Services\CheckoutService;
use App\Services\OrderStatusService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorefrontAuditFixesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that product detail page loads without crashing.
     */
    public function test_product_detail_page_loads_successfully_with_product_reviews(): void
    {
        $category = Category::create([
            'name' => 'Tote Bags',
            'slug' => 'tote-bags',
            'is_active' => true,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'sku' => 'TOTE-01',
            'slug' => 'elan-tote',
            'name' => 'Elan Tote',
            'price' => '5000',
            'stock' => 10,
            'is_active' => true,
        ]);

        $response = $this->get("/products/{$product->slug}");
        $response->assertStatus(200);
        $response->assertSee('Elan Tote');
    }

    /**
     * Test that Checkout ID sorting preserves the product ID keys and completes checkout.
     */
    public function test_checkout_works_with_multiple_items_and_proper_sorting(): void
    {
        $category = Category::create(['name' => 'Shoulder Bags', 'slug' => 'shoulder-bags']);
        $p1 = Product::create(['category_id' => $category->id, 'sku' => 'P1', 'slug' => 'p1', 'name' => 'Product 1', 'price' => '100', 'stock' => 10, 'is_active' => true]);
        $p2 = Product::create(['category_id' => $category->id, 'sku' => 'P2', 'slug' => 'p2', 'name' => 'Product 2', 'price' => '200', 'stock' => 5, 'is_active' => true]);

        $checkoutService = app(CheckoutService::class);

        $customerData = [
            'name' => 'Test Customer',
            'email' => 'test@customer.com',
            'phone' => '12345678',
            'address' => 'Test Address',
            'payment_method' => 'COD',
        ];

        // Cart map: product_id => quantity
        $cartProductIds = [
            $p2->id => 1,
            $p1->id => 2,
        ];

        $order = $checkoutService->placeOrder($customerData, $cartProductIds);

        $this->assertNotNull($order);
        $this->assertEquals('Pending', $order->order_status);
        $this->assertEquals(2, $order->items->count());
        
        $p1->refresh();
        $p2->refresh();
        $this->assertEquals(8, $p1->stock);
        $this->assertEquals(4, $p2->stock);
    }

    /**
     * Test that order cancellation returns product stocks and registers movement.
     */
    public function test_cancellation_restores_inventory_stock(): void
    {
        $category = Category::create(['name' => 'Shoulder Bags', 'slug' => 'shoulder-bags']);
        $p = Product::create(['category_id' => $category->id, 'sku' => 'P1', 'slug' => 'p1', 'name' => 'Product 1', 'price' => '100', 'stock' => 10, 'is_active' => true]);

        $order = Order::create([
            'order_number' => 'EES-TEST123',
            'customer_name' => 'Customer',
            'email' => 'customer@test.com',
            'phone' => '12345',
            'shipping_address' => 'Address',
            'total_amount' => '100',
            'payment_method' => 'COD',
            'payment_status' => 'Pending',
            'order_status' => 'Pending',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $p->id,
            'product_name' => $p->name,
            'price' => '100',
            'quantity' => 2,
        ]);

        // Simulating that stock was decremented during checkout
        $p->decrement('stock', 2);
        $this->assertEquals(8, $p->stock);

        $statusService = app(OrderStatusService::class);
        $statusService->transition($order, 'Cancelled', 'Customer requested cancellation.');

        $p->refresh();
        $this->assertEquals(10, $p->stock);

        $movement = \App\Models\InventoryMovement::where('order_id', $order->id)->where('type', 'cancel_return')->first();
        $this->assertNotNull($movement);
        $this->assertEquals(2, $movement->quantity_delta);
    }

    /**
     * Test that guest session cart merges into DB cart after login.
     */
    public function test_guest_cart_merges_on_login(): void
    {
        $category = Category::create(['name' => 'Shoulder Bags', 'slug' => 'shoulder-bags']);
        $p = Product::create(['category_id' => $category->id, 'sku' => 'P1', 'slug' => 'p1', 'name' => 'Product 1', 'price' => '100', 'stock' => 10, 'is_active' => true]);

        $user = User::create([
            'name' => 'User Name',
            'email' => 'user@test.com',
            'password' => bcrypt('password'),
        ]);

        // Put item in session guest cart
        session()->put('guest_cart', [
            $p->id => ['quantity' => 3]
        ]);

        $response = $this->post('/login', [
            'email' => 'user@test.com',
            'password' => 'password',
        ]);

        $response->assertRedirect();
        
        // Assert guest cart is merged to user cart in DB
        $cartItem = CartItem::where('user_id', $user->id)->where('product_id', $p->id)->first();
        $this->assertNotNull($cartItem);
        $this->assertEquals(3, $cartItem->quantity);
        $this->assertEmpty(session()->get('guest_cart'));
    }
}
