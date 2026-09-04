<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\MediaAsset;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaLibraryTest extends TestCase
{
    use RefreshDatabase;

    public function test_media_library_page_renders_and_syncs_images(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);

        // 1. Create a category and product with images
        $cat = Category::create(['name' => 'Bags', 'slug' => 'bags', 'image' => 'categories/bag-banner.webp']);
        $prod = Product::create([
            'category_id' => $cat->id,
            'name' => 'Leather Handbag',
            'slug' => 'leather-handbag',
            'sku' => 'BAG-01',
            'price' => 1500,
            'stock' => 5,
            'image' => 'products/leather-main.webp',
            'is_active' => true,
        ]);

        // Place a mock image on fake storage
        Storage::disk('public')->put('products/leather-main.webp', 'fake content');

        // Access media library index
        $response = $this->actingAs($admin)->get(route('admin.media.index'));

        $response->assertOk();
        $response->assertSee('Media Library');
        $response->assertSee('leather-main.webp');
        $response->assertSee('bag-banner.webp');

        // Assert that MediaAsset record was synced
        $this->assertDatabaseHas('media_assets', [
            'path' => 'products/leather-main.webp',
        ]);
    }

    public function test_media_usage_endpoint_detects_product_and_variant_usage(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);

        $cat = Category::create(['name' => 'Shoes', 'slug' => 'shoes']);
        $prod = Product::create([
            'category_id' => $cat->id,
            'name' => 'Sneakers',
            'slug' => 'sneakers',
            'sku' => 'SNK-01',
            'price' => 2000,
            'stock' => 10,
            'image' => 'products/sneakers-main.webp',
            'is_active' => true,
        ]);

        $variant = ProductVariant::create([
            'product_id' => $prod->id,
            'name' => 'Red Sneakers',
            'color_name' => 'Red',
            'color_code' => '#ff0000',
            'sku' => 'SNK-RED',
            'regular_price' => 2000,
            'stock' => 5,
            'image' => 'variants/sneakers-red.webp',
            'is_active' => true,
        ]);

        $mainAsset = MediaAsset::create([
            'disk' => 'public',
            'path' => 'products/sneakers-main.webp',
            'original_name' => 'sneakers-main.webp',
            'mime_type' => 'image/webp',
            'size' => 1024,
        ]);

        $variantAsset = MediaAsset::create([
            'disk' => 'public',
            'path' => 'variants/sneakers-red.webp',
            'original_name' => 'sneakers-red.webp',
            'mime_type' => 'image/webp',
            'size' => 2048,
        ]);

        // Query usage for main product image
        $responseMain = $this->actingAs($admin)->getJson(route('admin.media.usage', $mainAsset));
        $responseMain->assertOk();
        $responseMain->assertJsonPath('in_use', true);
        $responseMain->assertJsonFragment(['label' => 'Sneakers (SKU: SNK-01)']);

        // Query usage for variant image
        $responseVariant = $this->actingAs($admin)->getJson(route('admin.media.usage', $variantAsset));
        $responseVariant->assertOk();
        $responseVariant->assertJsonPath('in_use', true);
        $responseVariant->assertJsonFragment(['type' => 'Product Color Variant']);
    }

    public function test_delete_is_blocked_when_image_is_in_use(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);

        $cat = Category::create(['name' => 'Wallets', 'slug' => 'wallets']);
        $prod = Product::create([
            'category_id' => $cat->id,
            'name' => 'Leather Wallet',
            'slug' => 'leather-wallet',
            'sku' => 'WAL-01',
            'price' => 800,
            'stock' => 4,
            'image' => 'products/wallet.webp',
            'is_active' => true,
        ]);

        Storage::disk('public')->put('products/wallet.webp', 'image content');

        $asset = MediaAsset::create([
            'disk' => 'public',
            'path' => 'products/wallet.webp',
            'original_name' => 'wallet.webp',
            'mime_type' => 'image/webp',
            'size' => 500,
        ]);

        // Attempting to delete in-use media via JSON should return 422 with usage details
        $responseJson = $this->actingAs($admin)->deleteJson(route('admin.media.destroy', $asset));
        $responseJson->assertStatus(422);
        $responseJson->assertJsonPath('in_use', true);
        $responseJson->assertJsonPath('success', false);

        // Attempting to delete via standard web request should redirect back with error flash
        $responseWeb = $this->actingAs($admin)->delete(route('admin.media.destroy', $asset));
        $responseWeb->assertRedirect();
        $responseWeb->assertSessionHas('error');

        // File and record must still exist
        $this->assertDatabaseHas('media_assets', ['id' => $asset->id]);
        Storage::disk('public')->assertExists('products/wallet.webp');
    }

    public function test_delete_succeeds_when_image_is_unused(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);

        Storage::disk('public')->put('media/unused-promo.webp', 'promo content');

        $asset = MediaAsset::create([
            'disk' => 'public',
            'path' => 'media/unused-promo.webp',
            'original_name' => 'unused-promo.webp',
            'mime_type' => 'image/webp',
            'size' => 800,
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.media.destroy', $asset));
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('media_assets', ['id' => $asset->id]);
        Storage::disk('public')->assertMissing('media/unused-promo.webp');
    }
}
