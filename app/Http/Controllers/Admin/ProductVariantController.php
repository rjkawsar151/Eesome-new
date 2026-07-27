<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProductVariantController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $data = $this->data($request);
        $data['product_id'] = $product->id;
        if ($file = $request->file('variant_image')) {
            $data['image'] = $file->store('variants', 'public');
        }
        ProductVariant::create($data);

        return back()->with('success', 'Variant created.');
    }

    public function update(Request $request, Product $product, ProductVariant $variant)
    {
        abort_unless($variant->product_id === $product->id, 404);
        $data = $this->data($request, $variant);
        if ($file = $request->file('variant_image')) {
            if ($variant->image) {
                Storage::disk('public')->delete($variant->image);
            }
            $data['image'] = $file->store('variants', 'public');
        }
        $variant->update($data);

        return back()->with('success', 'Variant updated.');
    }

    public function destroy(Product $product, ProductVariant $variant)
    {
        abort_unless($variant->product_id === $product->id, 404);
        if ($variant->image) {
            Storage::disk('public')->delete($variant->image);
        }
        $variant->delete();

        return back()->with('success', 'Variant deleted.');
    }

    private function data(Request $request, ?ProductVariant $variant = null): array
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => ['required', 'string', 'max:100', Rule::unique('product_variants')->ignore($variant?->id)],
            'color' => 'nullable|string|max:100', 'size' => 'nullable|string|max:100', 'material' => 'nullable|string|max:100',
            'price_adjustment' => 'required|numeric|between:-999999,999999', 'stock' => 'required|integer|min:0|max:1000000',
            'sort_order' => 'required|integer|min:0', 'variant_image' => 'nullable|image|mimes:png,webp,jpg,jpeg|max:5120',
        ]);
        unset($data['variant_image']);
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
