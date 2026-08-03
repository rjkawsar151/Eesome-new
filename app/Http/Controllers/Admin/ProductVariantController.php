<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\OptimizedImageStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProductVariantController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $data = $this->data($request);
        $data['product_id'] = $product->id;
        if ($file = $request->file('variant_image')) {
            $data['image'] = app(OptimizedImageStorage::class)->store($file, 'variants', 1400);
        }
        DB::transaction(function () use ($data, $product) {
            if ($data['is_default']) $product->variants()->update(['is_default' => false]);
            ProductVariant::create($data);
            $product->update(['has_variants' => true]);
        });

        return back()->with('success', 'Variant created.');
    }

    public function update(Request $request, Product $product, ProductVariant $variant)
    {
        abort_unless($variant->product_id === $product->id, 404);
        $data = $this->data($request, $variant);
        if ($file = $request->file('variant_image')) {
            if ($variant->image) {
                app(OptimizedImageStorage::class)->delete($variant->image);
            }
            $data['image'] = app(OptimizedImageStorage::class)->store($file, 'variants', 1400);
        }
        DB::transaction(function () use ($data, $product, $variant) {
            if ($data['is_default']) $product->variants()->whereKeyNot($variant->id)->update(['is_default' => false]);
            $variant->update($data);
        });

        return back()->with('success', 'Variant updated.');
    }

    public function destroy(Product $product, ProductVariant $variant)
    {
        abort_unless($variant->product_id === $product->id, 404);
        abort_if($product->variants()->where('is_active', true)->count() < 2, 409, 'A variant product must retain at least one active color.');
        $variant->update(['is_active' => false, 'is_default' => false]);

        return back()->with('success', 'Variant deleted.');
    }

    private function data(Request $request, ?ProductVariant $variant = null): array
    {
        $data = $request->validate([
            'color_name' => 'required|string|max:100',
            'color_code' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'sku' => ['required', 'string', 'max:100', Rule::unique('product_variants')->ignore($variant?->id)],
            'regular_price' => 'required|numeric|min:0', 'sale_price' => 'nullable|numeric|min:0|lte:regular_price', 'stock' => 'required|integer|min:0|max:1000000',
            'sort_order' => 'required|integer|min:0', 'variant_image' => 'nullable|image|mimes:png,webp,jpg,jpeg|max:5120',
        ]);
        unset($data['variant_image']);
        $data['name'] = $data['color_name'];
        $data['color'] = $data['color_name'];
        $data['price_adjustment'] = 0;
        $data['is_active'] = $request->boolean('is_active');
        $data['is_default'] = $request->boolean('is_default');

        return $data;
    }
}
