<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('products') || ! Schema::hasColumn('products', 'slug')) {
            return;
        }

        DB::table('products')
            ->whereNull('slug')
            ->orWhere('slug', '')
            ->orderBy('id')
            ->select(['id', 'name'])
            ->chunkById(100, function ($products) {
                foreach ($products as $product) {
                    $base = Str::slug($product->name) ?: 'product';
                    $slug = $base;

                    if (DB::table('products')->where('slug', $slug)->where('id', '!=', $product->id)->exists()) {
                        $slug = $base.'-'.$product->id;
                    }

                    DB::table('products')->where('id', $product->id)->update(['slug' => $slug]);
                }
            });
    }

    public function down(): void
    {
        // Generated storefront slugs are intentionally preserved.
    }
};
