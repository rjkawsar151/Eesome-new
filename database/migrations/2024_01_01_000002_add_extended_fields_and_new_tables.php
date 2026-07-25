<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add extended columns and new entities.
     */
    public function up(): void
    {
        // 1. Extend categories
        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'slug')) {
                $table->string('slug')->nullable()->after('name');
            }
            if (!Schema::hasColumn('categories', 'is_active')) {
                $table->tinyInteger('is_active')->default(1)->after('image');
            }
            if (!Schema::hasColumn('categories', 'sort_order')) {
                $table->integer('sort_order')->default(0)->after('is_active');
            }
            if (!Schema::hasColumn('categories', 'meta_title')) {
                $table->string('meta_title')->nullable()->after('sort_order');
            }
            if (!Schema::hasColumn('categories', 'meta_description')) {
                $table->text('meta_description')->nullable()->after('meta_title');
            }
        });

        // 2. Extend products
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'sku')) {
                $table->string('sku')->nullable()->after('id');
            }
            if (!Schema::hasColumn('products', 'slug')) {
                $table->string('slug')->nullable()->after('name');
            }
            if (!Schema::hasColumn('products', 'discount_price')) {
                $table->decimal('discount_price', 10, 2)->nullable()->after('price');
            }
            if (!Schema::hasColumn('products', 'badge_text')) {
                $table->string('badge_text', 30)->nullable()->after('discount_price');
            }
            if (!Schema::hasColumn('products', 'is_active')) {
                $table->tinyInteger('is_active')->default(1)->after('is_preorder');
            }
            if (!Schema::hasColumn('products', 'sort_order')) {
                $table->integer('sort_order')->default(0)->after('is_active');
            }
            if (!Schema::hasColumn('products', 'meta_title')) {
                $table->string('meta_title')->nullable()->after('sort_order');
            }
            if (!Schema::hasColumn('products', 'meta_description')) {
                $table->text('meta_description')->nullable()->after('meta_title');
            }
        });

        // 3. Extend orders
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'order_number')) {
                $table->string('order_number', 100)->nullable()->after('id');
            }
            if (!Schema::hasColumn('orders', 'subtotal_amount')) {
                $table->decimal('subtotal_amount', 10, 2)->nullable()->after('total_amount');
            }
            if (!Schema::hasColumn('orders', 'shipping_charge')) {
                $table->decimal('shipping_charge', 10, 2)->nullable()->after('subtotal_amount');
            }
            if (!Schema::hasColumn('orders', 'payment_fee')) {
                $table->decimal('payment_fee', 10, 2)->default(0)->after('shipping_charge');
            }
            if (!Schema::hasColumn('orders', 'coupon_id')) {
                $table->integer('coupon_id')->nullable()->after('coupon_code');
            }
            if (!Schema::hasColumn('orders', 'notes')) {
                $table->text('notes')->nullable()->after('transaction_id');
            }
            if (!Schema::hasColumn('orders', 'status_changed_at')) {
                $table->timestamp('status_changed_at')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('orders', 'placed_from')) {
                $table->string('placed_from', 50)->nullable()->after('status_changed_at');
            }
        });

        // 4. Extend order_items with snapshots
        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'product_name')) {
                $table->string('product_name')->nullable()->after('product_id');
            }
            if (!Schema::hasColumn('order_items', 'product_sku')) {
                $table->string('product_sku')->nullable()->after('product_name');
            }
            if (!Schema::hasColumn('order_items', 'product_image')) {
                $table->string('product_image', 500)->nullable()->after('product_sku');
            }
            if (!Schema::hasColumn('order_items', 'line_total')) {
                $table->decimal('line_total', 10, 2)->nullable()->after('quantity');
            }
            if (!Schema::hasColumn('order_items', 'discount_amount')) {
                $table->decimal('discount_amount', 10, 2)->default(0)->after('line_total');
            }
        });

        // 5. Create product_images
        if (!Schema::hasTable('product_images')) {
            Schema::create('product_images', function (Blueprint $table) {
                $table->integer('id')->autoIncrement();
                $table->integer('product_id');
                $table->string('image_path', 500);
                $table->string('alt_text')->nullable();
                $table->integer('sort_order')->default(0);
                $table->tinyInteger('is_primary')->default(0);
                $table->timestamps();
            });
        }

        // 6. Create reviews
        if (!Schema::hasTable('reviews')) {
            Schema::create('reviews', function (Blueprint $table) {
                $table->integer('id')->autoIncrement();
                $table->integer('product_id');
                $table->integer('user_id')->nullable();
                $table->integer('order_item_id')->nullable();
                $table->tinyInteger('rating')->default(5);
                $table->string('title')->nullable();
                $table->text('content');
                $table->string('status', 20)->default('pending'); // pending, approved, rejected
                $table->tinyInteger('is_verified_purchase')->default(0);
                $table->timestamps();
            });
        }

        // 7. Create order_status_histories
        if (!Schema::hasTable('order_status_histories')) {
            Schema::create('order_status_histories', function (Blueprint $table) {
                $table->integer('id')->autoIncrement();
                $table->integer('order_id');
                $table->string('from_status', 50)->nullable();
                $table->string('to_status', 50);
                $table->integer('changed_by_user_id')->nullable();
                $table->text('note')->nullable();
                $table->timestamps();
            });
        }

        // 8. Create payment_transactions
        if (!Schema::hasTable('payment_transactions')) {
            Schema::create('payment_transactions', function (Blueprint $table) {
                $table->integer('id')->autoIncrement();
                $table->integer('order_id');
                $table->string('provider', 30); // bKash, COD
                $table->string('provider_transaction_id', 150)->nullable();
                $table->string('merchant_invoice', 150)->nullable();
                $table->decimal('amount', 10, 2);
                $table->string('status', 30)->default('pending');
                $table->json('request_payload')->nullable();
                $table->json('response_payload')->nullable();
                $table->timestamp('verified_at')->nullable();
                $table->timestamps();
            });
        }

        // 9. Create inventory_movements
        if (!Schema::hasTable('inventory_movements')) {
            Schema::create('inventory_movements', function (Blueprint $table) {
                $table->integer('id')->autoIncrement();
                $table->integer('product_id');
                $table->integer('order_id')->nullable();
                $table->string('type', 30); // sale, restock, adjustment, cancel_return
                $table->integer('quantity_delta');
                $table->integer('stock_before');
                $table->integer('stock_after');
                $table->string('reference', 100)->nullable();
                $table->integer('created_by_user_id')->nullable();
                $table->timestamps();
            });
        }

        // 10. Create page_views (user visiting states analytics)
        if (!Schema::hasTable('page_views')) {
            Schema::create('page_views', function (Blueprint $table) {
                $table->integer('id')->autoIncrement();
                $table->string('ip_address', 45)->nullable();
                $table->string('url', 500);
                $table->text('user_agent')->nullable();
                $table->integer('user_id')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
