<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the baseline migrations for legacy tables.
     */
    public function up(): void
    {
        // 1. categories
        if (!Schema::hasTable('categories')) {
            Schema::create('categories', function (Blueprint $table) {
                $table->integer('id')->autoIncrement();
                $table->string('name');
                $table->string('image', 500)->nullable();
                $table->timestamps();
            });
        }

        // 2. products
        if (!Schema::hasTable('products')) {
            Schema::create('products', function (Blueprint $table) {
                $table->integer('id')->autoIncrement();
                $table->integer('category_id')->nullable();
                $table->string('name');
                $table->text('description')->nullable();
                $table->decimal('price', 10, 2);
                $table->integer('stock')->default(0);
                $table->string('image', 500)->nullable();
                $table->tinyInteger('is_featured')->default(0);
                $table->tinyInteger('is_new')->default(0);
                $table->tinyInteger('is_sold_out')->default(0);
                $table->tinyInteger('is_preorder')->default(0);
                $table->timestamps();
            });
        }

        // 3. orders
        if (!Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $table) {
                $table->integer('id')->autoIncrement();
                $table->integer('user_id')->nullable();
                $table->string('customer_name');
                $table->string('email');
                $table->string('phone', 100);
                $table->text('shipping_address');
                $table->decimal('total_amount', 10, 2);
                $table->decimal('discount_amount', 10, 2)->default(0);
                $table->string('coupon_code', 100)->nullable();
                $table->string('payment_method', 50)->default('COD');
                $table->string('payment_status', 50)->default('Pending');
                $table->string('order_status', 50)->default('Pending');
                $table->string('transaction_id', 255)->nullable();
                $table->timestamps();
            });
        }

        // 4. order_items
        if (!Schema::hasTable('order_items')) {
            Schema::create('order_items', function (Blueprint $table) {
                $table->integer('id')->autoIncrement();
                $table->integer('order_id');
                $table->integer('product_id')->nullable();
                $table->decimal('price', 10, 2);
                $table->integer('quantity')->default(1);
                $table->timestamps();
            });
        }

        // 5. wishlist (singular table name)
        if (!Schema::hasTable('wishlist')) {
            Schema::create('wishlist', function (Blueprint $table) {
                $table->integer('id')->autoIncrement();
                $table->integer('user_id');
                $table->integer('product_id');
                $table->timestamps();
            });
        }

        // 6. cart_items
        if (!Schema::hasTable('cart_items')) {
            Schema::create('cart_items', function (Blueprint $table) {
                $table->integer('id')->autoIncrement();
                $table->integer('user_id');
                $table->integer('product_id');
                $table->integer('quantity')->default(1);
                $table->tinyInteger('is_abandoned_notified')->default(0);
                $table->timestamps();
            });
        }

        // 7. coupons
        if (!Schema::hasTable('coupons')) {
            Schema::create('coupons', function (Blueprint $table) {
                $table->integer('id')->autoIncrement();
                $table->string('code', 100)->unique();
                $table->string('discount_type', 50);
                $table->decimal('discount_value', 10, 2);
                $table->decimal('min_order_amount', 10, 2)->default(0);
                $table->date('expiry_date')->nullable();
                $table->integer('usage_limit')->nullable();
                $table->integer('used_count')->default(0);
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });
        }

        // 8. testimonials
        if (!Schema::hasTable('testimonials')) {
            Schema::create('testimonials', function (Blueprint $table) {
                $table->integer('id')->autoIncrement();
                $table->string('name');
                $table->text('content');
                $table->string('image', 500)->nullable();
                $table->tinyInteger('rating')->default(5);
                $table->tinyInteger('is_active')->default(1);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        // 9. blog_posts
        if (!Schema::hasTable('blog_posts')) {
            Schema::create('blog_posts', function (Blueprint $table) {
                $table->integer('id')->autoIncrement();
                $table->string('name')->nullable(); // or title
                $table->string('title')->nullable();
                $table->text('content');
                $table->string('image', 500)->nullable();
                $table->timestamps();
            });
        }

        // 10. otp_codes
        if (!Schema::hasTable('otp_codes')) {
            Schema::create('otp_codes', function (Blueprint $table) {
                $table->integer('id')->autoIncrement();
                $table->integer('user_id')->nullable();
                $table->string('code', 100);
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();
            });
        }

        // 11. site_settings
        if (!Schema::hasTable('site_settings')) {
            Schema::create('site_settings', function (Blueprint $table) {
                $table->integer('id')->autoIncrement();
                $table->string('setting_key', 255)->unique();
                $table->text('setting_value')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Safe policy: preserve legacy production tables
    }
};
