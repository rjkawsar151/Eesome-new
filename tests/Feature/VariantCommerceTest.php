<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Services\CheckoutService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class VariantCommerceTest extends TestCase
{
    use RefreshDatabase;

    private function catalog(): array
    {
        $category = Category::create(['name' => 'Bags', 'slug' => 'bags', 'is_active' => true]);
        $product = Product::create(['category_id' => $category->id, 'sku' => 'BAG', 'slug' => 'variant-bag', 'name' => 'Variant Bag', 'price' => 1000, 'stock' => 0, 'has_variants' => true, 'is_active' => true]);
        $pink = ProductVariant::create(['product_id' => $product->id, 'name' => 'Pink', 'color_name' => 'Pink', 'color' => 'Pink', 'color_code' => '#ff99bb', 'sku' => 'BAG-PINK', 'regular_price' => 1200, 'price_adjustment' => 0, 'stock' => 3, 'is_active' => true, 'is_default' => true]);
        $black = ProductVariant::create(['product_id' => $product->id, 'name' => 'Black', 'color_name' => 'Black', 'color' => 'Black', 'color_code' => '#111111', 'sku' => 'BAG-BLACK', 'regular_price' => 1100, 'price_adjustment' => 0, 'stock' => 2, 'is_active' => true]);
        return compact('product', 'pink', 'black');
    }

    public function test_variant_product_requires_owned_available_variant(): void
    {
        ['product' => $product, 'pink' => $pink] = $this->catalog();
        $this->from('/products/'.$product->slug)->post('/cart', ['product_id' => $product->id, 'quantity' => 1])->assertSessionHasErrors('variant_id');
        $this->post('/cart', ['product_id' => $product->id, 'variant_id' => $pink->id, 'quantity' => 1])->assertSessionHasNoErrors();
    }

    public function test_single_variant_product_auto_selects_variant_and_hides_dialog(): void
    {
        $category = Category::create(['name' => 'Shoes', 'slug' => 'shoes', 'is_active' => true]);
        $product = Product::create(['category_id' => $category->id, 'sku' => 'SHOE', 'slug' => 'single-variant-shoe', 'name' => 'Single Variant Shoe', 'price' => 1500, 'stock' => 0, 'has_variants' => true, 'is_active' => true]);
        $red = ProductVariant::create(['product_id' => $product->id, 'name' => 'Red', 'color_name' => 'Red', 'color' => 'Red', 'sku' => 'SHOE-RED', 'regular_price' => 1500, 'stock' => 5, 'is_active' => true, 'is_default' => true]);

        $this->get('/products/'.$product->slug)
            ->assertOk()
            ->assertDontSee('id="variant-dialog"', false);

        $this->post('/cart', ['product_id' => $product->id, 'quantity' => 1])
            ->assertSessionHasNoErrors();

        $this->assertArrayHasKey($product->id.':'.$red->id, session('guest_cart'));
    }

    public function test_two_colors_are_separate_guest_cart_lines(): void
    {
        ['product' => $product, 'pink' => $pink, 'black' => $black] = $this->catalog();
        $this->post('/cart', ['product_id' => $product->id, 'variant_id' => $pink->id]);
        $this->post('/cart', ['product_id' => $product->id, 'variant_id' => $black->id]);
        $this->assertCount(2, session('guest_cart'));
    }

    public function test_checkout_snapshots_color_and_deducts_only_variant_stock(): void
    {
        Notification::fake();
        ['product' => $product, 'pink' => $pink] = $this->catalog();
        $order = app(CheckoutService::class)->placeOrder(['name' => 'Customer', 'email' => 'c@example.com', 'phone' => '123', 'address' => 'Dhaka', 'payment_method' => 'COD'], [['product_id' => $product->id, 'variant_id' => $pink->id, 'quantity' => 2]]);
        $item = $order->items()->first();
        $this->assertSame('Pink', $item->selected_color_name);
        $this->assertSame('BAG-PINK', $item->product_sku);
        $this->assertSame(1, $pink->fresh()->stock);
        $this->assertSame(0, $product->fresh()->stock);
    }

    public function test_admin_delete_permanently_removes_product_and_images(): void
    {
        ['product' => $product] = $this->catalog();
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->delete('/admin/products/'.$product->id)->assertRedirect();
        $this->assertNull(Product::find($product->id));
        $this->assertSame(0, \App\Models\ProductVariant::where('product_id', $product->id)->count());
    }

    public function test_variant_product_page_renders_color_selector(): void
    {
        ['product' => $product] = $this->catalog();

        $this->get('/products/'.$product->slug)
            ->assertOk()
            ->assertSee('Choose your color')
            ->assertSee('BAG-PINK');
    }

    public function test_admin_can_add_a_color_variant(): void
    {
        $category = Category::create(['name' => 'Bags', 'slug' => 'bags', 'is_active' => true]);
        $product = Product::create(['category_id' => $category->id, 'sku' => 'PLAIN', 'slug' => 'plain-bag', 'name' => 'Plain Bag', 'price' => 900, 'stock' => 2, 'has_variants' => false, 'is_active' => true]);
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post('/admin/products/'.$product->id.'/variants', [
            'color_name' => 'Rose',
            'color_code' => '#dd7799',
            'sku' => 'PLAIN-ROSE',
            'regular_price' => 950,
            'sale_price' => 900,
            'stock' => 4,
            'sort_order' => 0,
            'is_active' => '1',
            'is_default' => '1',
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('product_variants', ['product_id' => $product->id, 'color_name' => 'Rose', 'sku' => 'PLAIN-ROSE']);
        $this->assertTrue($product->fresh()->has_variants);
    }


    public function test_guest_cart_page_renders_multiple_variant_lines(): void
    {
        ['product' => $product, 'pink' => $pink, 'black' => $black] = $this->catalog();
        $this->post('/cart', ['product_id' => $product->id, 'variant_id' => $pink->id, 'quantity' => 1]);
        $this->post('/cart', ['product_id' => $product->id, 'variant_id' => $black->id, 'quantity' => 1]);

        $this->get('/cart')
            ->assertOk()
            ->assertSee('Color:')
            ->assertSee('BAG-PINK')
            ->assertSee('BAG-BLACK');
    }


    public function test_buy_now_with_variant_adds_line_and_redirects_to_checkout(): void
    {
        ['product' => $product, 'pink' => $pink] = $this->catalog();

        $this->post('/cart', [
            'product_id' => $product->id,
            'variant_id' => $pink->id,
            'quantity' => 1,
            'buy_now' => '1',
        ])->assertRedirect('/checkout');

        $this->assertArrayHasKey($product->id.':'.$pink->id, session('guest_cart'));
    }

    public function test_ajax_add_to_cart_returns_json_and_cart_count(): void
    {
        ['product' => $product, 'pink' => $pink] = $this->catalog();

        $response = $this->postJson('/cart', [
            'product_id' => $product->id,
            'variant_id' => $pink->id,
            'quantity'   => 2,
        ]);

        $response->assertOk()
            ->assertJson([
                'success'    => true,
                'message'    => 'Successfully added to cart.',
                'cart_count' => 2,
            ]);
    }

    public function test_variant_product_page_resolves_image_and_data_attributes(): void
    {
        ['product' => $product, 'pink' => $pink, 'black' => $black] = $this->catalog();
        $pink->update(['image' => 'variants/pink-bag.webp']);
        \App\Models\ProductImage::create([
            'product_id' => $product->id,
            'image_path' => 'products/black-bag.webp',
            'alt_text' => 'Black',
            'sort_order' => 2,
            'is_primary' => false,
        ]);

        $response = $this->get('/products/'.$product->slug);
        $response->assertOk();
        $response->assertSee('data-variant-id="'.$pink->id.'"', false);
        $response->assertSee('data-variant-id="'.$black->id.'"', false);
        $response->assertSee('data-color="Pink"', false);
        $response->assertSee('data-color="Black"', false);
        $response->assertSee('data-image=', false);
        $response->assertSee('pink-bag.webp', false);
        $response->assertSee('black-bag.webp', false);
        $response->assertSee('id="main-image-badge"', false);
    }
}

