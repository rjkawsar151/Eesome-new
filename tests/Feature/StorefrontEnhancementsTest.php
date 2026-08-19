<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\ShippingMethod;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class StorefrontEnhancementsTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_sorting_orders_in_stock_first_then_preorder_and_out_of_stock(): void
    {
        $category = Category::create(['name' => 'Collection', 'slug' => 'collection', 'is_active' => true]);

        // 1. In-stock simple product created first (lower ID)
        $inStockOld = Product::create([
            'category_id' => $category->id,
            'name' => 'In Stock Old',
            'slug' => 'in-stock-old',
            'sku' => 'IN-OLD',
            'price' => 1000,
            'stock' => 10,
            'is_sold_out' => false,
            'is_preorder' => false,
            'has_variants' => false,
            'is_active' => true,
        ]);

        // 2. Out of stock product created second
        $outOfStock = Product::create([
            'category_id' => $category->id,
            'name' => 'Out Of Stock',
            'slug' => 'out-of-stock',
            'sku' => 'OUT',
            'price' => 1200,
            'stock' => 0,
            'is_sold_out' => true,
            'is_preorder' => false,
            'has_variants' => false,
            'is_active' => true,
        ]);

        // 3. Preorder product created third
        $preorderProduct = Product::create([
            'category_id' => $category->id,
            'name' => 'Preorder Product',
            'slug' => 'preorder-product',
            'sku' => 'PRE',
            'price' => 1500,
            'stock' => 0,
            'is_sold_out' => false,
            'is_preorder' => true,
            'has_variants' => false,
            'is_active' => true,
        ]);

        // 4. In-stock variant product created fourth (higher ID)
        $inStockNewVariant = Product::create([
            'category_id' => $category->id,
            'name' => 'In Stock New Variant',
            'slug' => 'in-stock-new-variant',
            'sku' => 'IN-NEW-VAR',
            'price' => 2000,
            'stock' => 0,
            'is_sold_out' => false,
            'is_preorder' => false,
            'has_variants' => true,
            'is_active' => true,
        ]);
        ProductVariant::create([
            'product_id' => $inStockNewVariant->id,
            'name' => 'Gold',
            'color_name' => 'Gold',
            'sku' => 'IN-NEW-VAR-GOLD',
            'regular_price' => 2000,
            'stock' => 5,
            'is_active' => true,
            'is_default' => true,
        ]);

        $sortedProducts = Product::where('is_active', true)->orderByInStockFirst()->get();

        // The order must be: In Stock New Variant (id 4), In Stock Old (id 1), Preorder Product (id 3), Out Of Stock (id 2)
        $this->assertSame($inStockNewVariant->id, $sortedProducts[0]->id);
        $this->assertSame($inStockOld->id, $sortedProducts[1]->id);
        $this->assertSame($preorderProduct->id, $sortedProducts[2]->id);
        $this->assertSame($outOfStock->id, $sortedProducts[3]->id);
    }

    public function test_preorder_product_with_zero_stock_variant_allows_add_to_cart(): void
    {
        $category = Category::create(['name' => 'Preorder Category', 'slug' => 'preorder-cat', 'is_active' => true]);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Preorder Handbag',
            'slug' => 'preorder-handbag',
            'sku' => 'PRE-HB',
            'price' => 3000,
            'stock' => 0,
            'is_preorder' => true,
            'available_for_preorder' => true,
            'has_variants' => true,
            'is_active' => true,
        ]);
        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'name' => 'Emerald Green',
            'color_name' => 'Emerald Green',
            'sku' => 'PRE-HB-EMR',
            'regular_price' => 3000,
            'stock' => 0,
            'is_active' => true,
            'is_default' => true,
        ]);

        $response = $this->post('/cart', [
            'product_id' => $product->id,
            'variant_id' => $variant->id,
            'quantity' => 1,
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertArrayHasKey($product->id.':'.$variant->id, session('guest_cart'));
    }

    public function test_product_with_mixed_stock_variants_allows_adding_zero_stock_color(): void
    {
        $category = Category::create(['name' => 'Pouches', 'slug' => 'pouches', 'is_active' => true]);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'The Origami Pouch',
            'slug' => 'the-origami-pouch',
            'sku' => 'ORI-01',
            'price' => 2500,
            'stock' => 5,
            'is_preorder' => false,
            'has_variants' => true,
            'is_active' => true,
        ]);

        // In-stock variant
        $blackVariant = ProductVariant::create([
            'product_id' => $product->id,
            'name' => 'Black',
            'color_name' => 'Black',
            'sku' => 'ORI-01-BLK',
            'regular_price' => 2500,
            'stock' => 5,
            'is_active' => true,
            'is_default' => true,
        ]);

        // 0-stock / preorder variant (e.g. "merron" / maroon)
        $merronVariant = ProductVariant::create([
            'product_id' => $product->id,
            'name' => 'Merron',
            'color_name' => 'Merron',
            'sku' => 'ORI-01-MRN',
            'regular_price' => 2500,
            'stock' => 0,
            'is_active' => true,
        ]);

        // 1. Adding merron variant by ID should succeed as preorder without 500 error
        $response = $this->postJson('/cart', [
            'product_id' => $product->id,
            'variant_id' => $merronVariant->id,
            'quantity' => 1,
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Successfully added to cart.',
            ]);

        $this->assertArrayHasKey($product->id.':'.$merronVariant->id, session('guest_cart'));

        // 2. Adding merron variant by color string name should also resolve and succeed
        $responseStr = $this->postJson('/cart', [
            'product_id' => $product->id,
            'variant_id' => 'Merron',
            'quantity' => 1,
        ]);

        $responseStr->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Successfully added to cart.',
            ]);
    }

    public function test_product_detail_page_renders_slider_and_variant_elements(): void
    {
        $category = Category::create(['name' => 'Bags', 'slug' => 'bags', 'is_active' => true]);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Luxury Tote',
            'slug' => 'luxury-tote',
            'sku' => 'TOTE-01',
            'price' => 4500,
            'stock' => 10,
            'has_variants' => true,
            'is_active' => true,
        ]);
        $variantRed = ProductVariant::create([
            'product_id' => $product->id,
            'name' => 'Crimson',
            'color_name' => 'Crimson',
            'sku' => 'TOTE-01-RED',
            'regular_price' => 4500,
            'stock' => 4,
            'is_active' => true,
            'is_default' => true,
        ]);
        $variantBlue = ProductVariant::create([
            'product_id' => $product->id,
            'name' => 'Navy',
            'color_name' => 'Navy',
            'sku' => 'TOTE-01-BLUE',
            'regular_price' => 4700,
            'stock' => 6,
            'is_active' => true,
        ]);

        ProductImage::create([
            'product_id' => $product->id,
            'image_path' => 'products/tote-red.webp',
            'alt_text' => 'Crimson',
            'sort_order' => 1,
            'is_primary' => true,
        ]);
        ProductImage::create([
            'product_id' => $product->id,
            'image_path' => 'products/tote-blue.webp',
            'alt_text' => 'Navy',
            'sort_order' => 2,
            'is_primary' => false,
        ]);

        $response = $this->get('/products/'.$product->slug);
        $response->assertOk();
        $response->assertSee('slider-nav-prev', false);
        $response->assertSee('slider-nav-next', false);
        $response->assertSee('window.productSlides', false);
        $response->assertSee('Crimson', false);
        $response->assertSee('Navy', false);
    }

    public function test_checkout_validates_transaction_id_when_payment_method_requires_it(): void
    {
        Notification::fake();
        $category = Category::create(['name' => 'Accessories', 'slug' => 'acc', 'is_active' => true]);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Leather Wallet',
            'slug' => 'leather-wallet',
            'sku' => 'WAL-01',
            'price' => 1200,
            'stock' => 10,
            'is_active' => true,
        ]);

        $shipping = ShippingMethod::create([
            'name' => 'Inside Dhaka',
            'code' => 'INSIDE_DHAKA',
            'base_charge' => 60,
            'charge_type' => 'fixed',
            'cod_available' => true,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $bkash = PaymentMethod::create([
            'name' => 'bKash Merchant',
            'code' => 'BKASH',
            'type' => 'manual',
            'account_name' => 'Eesome Official',
            'account_number' => '01700000000',
            'requires_transaction_id' => true,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        // Add to cart
        $this->post('/cart', ['product_id' => $product->id, 'quantity' => 1]);

        // 1. Attempt checkout with BKASH without transaction_id -> should fail validation
        $responseFail = $this->post('/checkout', [
            'name' => 'Test User',
            'email' => 'user@test.com',
            'phone' => '01711111111',
            'district' => 'Dhaka',
            'thana' => 'Gulshan',
            'post_office' => 'Gulshan',
            'post_code' => '1212',
            'address' => 'House 1, Road 1',
            'shipping_method' => $shipping->code,
            'payment_method' => $bkash->code,
            // 'transaction_id' is missing
        ]);
        $responseFail->assertSessionHasErrors('transaction_id');

        // 2. Submit with valid transaction_id -> should succeed and save transaction_id
        $responseSuccess = $this->post('/checkout', [
            'name' => 'Test User',
            'email' => 'user@test.com',
            'phone' => '01711111111',
            'district' => 'Dhaka',
            'thana' => 'Gulshan',
            'post_office' => 'Gulshan',
            'post_code' => '1212',
            'address' => 'House 1, Road 1',
            'shipping_method' => $shipping->code,
            'payment_method' => $bkash->code,
            'transaction_id' => 'BKASH_TX_987654',
        ]);
        $responseSuccess->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'payment_method' => 'BKASH',
            'transaction_id' => 'BKASH_TX_987654',
        ]);
    }

    public function test_checkout_allows_cod_without_transaction_id(): void
    {
        Notification::fake();
        $category = Category::create(['name' => 'Accessories', 'slug' => 'acc-cod', 'is_active' => true]);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Clutch Bag',
            'slug' => 'clutch-bag',
            'sku' => 'CLU-01',
            'price' => 1800,
            'stock' => 5,
            'is_active' => true,
        ]);

        $shipping = ShippingMethod::firstOrCreate(
            ['code' => 'INSIDE_DHAKA_COD'],
            [
                'name' => 'Inside Dhaka',
                'base_charge' => 60,
                'charge_type' => 'fixed',
                'cod_available' => true,
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        $cod = PaymentMethod::where('code', 'COD')->first();
        if (!$cod) {
            $cod = PaymentMethod::create([
                'name' => 'Cash On Delivery',
                'code' => 'COD',
                'type' => 'manual',
                'requires_transaction_id' => false,
                'is_active' => true,
                'sort_order' => 2,
            ]);
        } else {
            $cod->update(['requires_transaction_id' => false, 'is_active' => true]);
        }

        $this->post('/cart', ['product_id' => $product->id, 'quantity' => 1]);

        $response = $this->post('/checkout', [
            'name' => 'Test User',
            'email' => 'user@test.com',
            'phone' => '01711111111',
            'district' => 'Dhaka',
            'thana' => 'Gulshan',
            'post_office' => 'Gulshan',
            'post_code' => '1212',
            'address' => 'House 1, Road 1',
            'shipping_method' => $shipping->code,
            'payment_method' => $cod->code,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'payment_method' => 'COD',
            'transaction_id' => null,
        ]);
    }

    public function test_custom_500_view_renders_properly(): void
    {
        $view = view('errors.500')->render();
        $this->assertStringContainsString('Something went wrong on our end.', $view);
        $this->assertStringContainsString('Return to Home', $view);
    }
}
