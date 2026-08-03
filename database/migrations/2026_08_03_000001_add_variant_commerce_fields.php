<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'has_variants')) $table->boolean('has_variants')->default(false)->after('stock');
        });
        Schema::table('product_variants', function (Blueprint $table) {
            if (! Schema::hasColumn('product_variants', 'color_name')) $table->string('color_name')->nullable()->after('name');
            if (! Schema::hasColumn('product_variants', 'color_code')) $table->string('color_code', 7)->nullable()->after('color_name');
            if (! Schema::hasColumn('product_variants', 'regular_price')) $table->decimal('regular_price', 12, 2)->nullable()->after('color_code');
            if (! Schema::hasColumn('product_variants', 'sale_price')) $table->decimal('sale_price', 12, 2)->nullable()->after('regular_price');
            if (! Schema::hasColumn('product_variants', 'is_default')) $table->boolean('is_default')->default(false)->after('is_active');
        });
        Schema::table('cart_items', function (Blueprint $table) {
            if (! Schema::hasColumn('cart_items', 'variant_id')) $table->unsignedBigInteger('variant_id')->nullable()->after('product_id')->index();
        });
        Schema::table('order_items', function (Blueprint $table) {
            if (! Schema::hasColumn('order_items', 'variant_id')) $table->unsignedBigInteger('variant_id')->nullable()->after('product_id');
            if (! Schema::hasColumn('order_items', 'selected_color_name')) $table->string('selected_color_name')->nullable()->after('product_sku');
            if (! Schema::hasColumn('order_items', 'selected_color_code')) $table->string('selected_color_code', 7)->nullable()->after('selected_color_name');
        });
        Schema::table('inventory_movements', function (Blueprint $table) {
            if (! Schema::hasColumn('inventory_movements', 'variant_id')) $table->unsignedBigInteger('variant_id')->nullable()->after('product_id');
        });

        DB::table('product_variants')->whereNull('color_name')->update(['color_name' => DB::raw('color')]);
        DB::table('products')->whereExists(function ($query) {
            $query->selectRaw('1')->from('product_variants')->whereColumn('product_variants.product_id', 'products.id');
        })->update(['has_variants' => true]);
    }

    public function down(): void {}
};
