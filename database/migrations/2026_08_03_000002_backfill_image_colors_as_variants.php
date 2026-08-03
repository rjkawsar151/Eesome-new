<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('product_images', 'color_name')) {
            return;
        }

        $products = DB::table('products')
            ->join('product_images', 'product_images.product_id', '=', 'products.id')
            ->whereNotNull('product_images.color_name')
            ->whereRaw("TRIM(product_images.color_name) <> ''")
            ->select('products.id', 'products.sku', 'products.price', 'products.discount_price', 'products.stock')
            ->distinct()
            ->get();

        foreach ($products as $product) {
            $images = DB::table('product_images')
                ->where('product_id', $product->id)
                ->whereNotNull('color_name')
                ->whereRaw("TRIM(color_name) <> ''")
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get()
                ->unique(fn ($image) => Str::lower(trim($image->color_name)))
                ->values();

            $created = 0;
            foreach ($images as $index => $image) {
                $color = trim($image->color_name);
                $exists = DB::table('product_variants')
                    ->where('product_id', $product->id)
                    ->whereRaw('LOWER(TRIM(color_name)) = ?', [Str::lower($color)])
                    ->exists();

                if ($exists) {
                    continue;
                }

                $baseSku = $product->sku ?: 'PRODUCT-'.$product->id;
                $suffix = Str::upper(Str::slug($color, '-')) ?: 'COLOR-'.($index + 1);
                $sku = Str::limit($baseSku.'-'.$suffix, 94, '');
                $candidate = $sku;
                $counter = 2;
                while (DB::table('product_variants')->where('sku', $candidate)->exists()) {
                    $candidate = Str::limit($sku, 90, '').'-'.$counter++;
                }

                DB::table('product_variants')->insert([
                    'product_id' => $product->id,
                    'name' => $color,
                    'color' => $color,
                    'color_name' => $color,
                    'color_code' => '#C8A2C8',
                    'sku' => $candidate,
                    'price_adjustment' => 0,
                    'regular_price' => $product->price,
                    'sale_price' => $product->discount_price,
                    'stock' => max(0, (int) $product->stock),
                    'image' => $image->image_path,
                    'is_active' => true,
                    'is_default' => $index === 0,
                    'sort_order' => $index,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $created++;
            }

            if ($created > 0 || DB::table('product_variants')->where('product_id', $product->id)->exists()) {
                DB::table('products')->where('id', $product->id)->update(['has_variants' => true]);
            }
        }
    }

    public function down(): void
    {
        // Imported variants contain live commerce data and are intentionally preserved.
    }
};
