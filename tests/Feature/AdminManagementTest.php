<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_and_customers_cannot_access_admin_management(): void
    {
        $this->get('/admin/products')->assertRedirect('/login');

        $customer = User::factory()->create(['role' => 'user']);
        $this->actingAs($customer)->get('/admin/products')->assertForbidden();
    }

    public function test_admin_can_open_all_management_modules(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        foreach (['/admin/products', '/admin/categories', '/admin/reviews', '/admin/blog', '/admin/settings', '/admin/visitors'] as $url) {
            $this->actingAs($admin)->get($url)->assertOk();
        }
    }

    public function test_admin_can_create_a_product_and_assign_it_to_the_hero(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::create(['name' => 'Totes', 'slug' => 'totes', 'is_active' => true, 'sort_order' => 1]);

        $this->actingAs($admin)->post('/admin/products', [
            'category_id' => $category->id,
            'name' => 'Hero Tote',
            'slug' => 'hero-tote',
            'sku' => 'HERO-001',
            'price' => 5000,
            'stock' => 4,
            'sort_order' => 1,
            'is_active' => 1,
            'is_featured' => 1,
        ])->assertRedirect();

        $this->assertDatabaseHas('products', ['slug' => 'hero-tote', 'is_featured' => 1, 'is_active' => 1]);
    }

    public function test_staff_roles_are_limited_to_their_assigned_modules(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $this->actingAs($manager)->get('/admin/orders')->assertOk();
        $this->actingAs($manager)->get('/admin/products')->assertOk();
        $this->actingAs($manager)->get('/admin/users')->assertForbidden();
        $this->actingAs($manager)->get('/admin/settings')->assertForbidden();
        $this->actingAs($manager)->get('/admin/visitors')->assertForbidden();

        $editor = User::factory()->create(['role' => 'content editor']);
        $this->actingAs($editor)->get('/admin/blog')->assertOk();
        $this->actingAs($editor)->get('/admin/products')->assertOk();
        $this->actingAs($editor)->get('/admin/orders')->assertForbidden();
    }

    public function test_successful_admin_changes_are_logged_without_passwords(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->put('/admin/settings', [
            'store_name' => 'EEsome Test',
            'password' => 'must-not-be-logged',
        ])->assertRedirect();

        $this->assertDatabaseHas('admin_activity_logs', ['admin_id' => $admin->id, 'action' => 'admin.settings.update']);
        $payload = \App\Models\AdminActivityLog::latest('id')->value('new_values');
        $this->assertStringNotContainsString('must-not-be-logged', json_encode($payload));
    }

    public function test_product_creation_autogenerates_sku_and_works_without_display_order(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::create(['name' => 'Shoulder Bags', 'slug' => 'shoulder-bags', 'is_active' => true, 'sort_order' => 1]);

        $response = $this->actingAs($admin)->post('/admin/products', [
            'category_id' => $category->id,
            'name' => 'Azure Crescent Bag',
            'slug' => 'azure-crescent-bag',
            'price' => 4500,
            'stock' => 5,
            'is_active' => 1,
        ]);

        $response->assertRedirect();
        $product = \App\Models\Product::where('slug', 'azure-crescent-bag')->first();
        $this->assertNotNull($product);
        $this->assertNotEmpty($product->sku);
        $this->assertStringStartsWith('EES-', $product->sku);
        $this->assertSame(0, $product->sort_order);
    }

    public function test_product_with_zero_stock_is_preorder_only(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::create(['name' => 'Clutches', 'slug' => 'clutches', 'is_active' => true, 'sort_order' => 1]);

        $response = $this->actingAs($admin)->post('/admin/products', [
            'category_id' => $category->id,
            'name' => 'Golden Velvet Clutch',
            'slug' => 'golden-velvet-clutch',
            'price' => 3800,
            'stock' => 0,
            'is_active' => 1,
        ]);

        $response->assertRedirect();
        $product = \App\Models\Product::where('slug', 'golden-velvet-clutch')->first();
        $this->assertNotNull($product);
        $this->assertTrue($product->is_preorder);
        $this->assertTrue($product->available_for_preorder);
        $this->assertSame(['text' => 'PREORDER', 'type' => 'warning'], $product->badge_info);
    }
}

