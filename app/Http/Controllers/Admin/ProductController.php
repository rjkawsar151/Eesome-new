<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Tag;
use App\Services\OptimizedImageStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $r)
    {
        $q = Product::with('category')->withCount('images')->orderBy('sort_order')->latest('id');
        if ($r->filled('search')) {
            $s = $r->search;
            $q->where(fn ($x) => $x->where('name', 'like', "%$s%")->orWhere('sku', 'like', "%$s%"));
        }if ($r->filled('category_id')) {
            $q->where('category_id', $r->category_id);
        }if ($r->filled('state')) {
            $q->where($r->state === 'featured' ? 'is_featured' : 'is_active', 1);
        }

        return view('admin.products.index', ['products' => $q->paginate(20)->withQueryString(), 'categories' => Category::orderBy('name')->get()]);
    }

    public function hero()
    {
        $products = Product::with(['category', 'images'])
            ->where('is_active', true)
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.hero-products.edit', compact('products'));
    }

    public function updateHero(Request $request)
    {
        $data = $request->validate([
            'featured' => ['nullable', 'array'],
            'featured.*' => ['integer', 'exists:products,id'],
            'sort_order' => ['nullable', 'array'],
            'sort_order.*' => ['nullable', 'integer', 'min:0'],
        ]);

        $featuredIds = collect($data['featured'] ?? [])->map(fn ($id) => (int) $id);
        $sortOrders = $data['sort_order'] ?? [];

        DB::transaction(function () use ($featuredIds, $sortOrders) {
            Product::where('is_featured', true)->update(['is_featured' => false]);
            Product::where('is_active', true)->get()->each(function (Product $product) use ($featuredIds, $sortOrders) {
                $updates = ['is_featured' => $featuredIds->contains($product->id)];
                if (array_key_exists($product->id, $sortOrders) && $sortOrders[$product->id] !== null) {
                    $updates['sort_order'] = (int) $sortOrders[$product->id];
                }
                $product->update($updates);
            });
        });

        return back()->with('success', 'Homepage hero products updated.');
    }
    public function create()
    {
        return view('admin.products.form', ['product' => new Product, 'categories' => Category::orderBy('name')->get(), 'brands' => Brand::where('is_active', true)->orderBy('name')->get(), 'tags' => Tag::orderBy('name')->get()]);
    }

    public function store(Request $r)
    {
        $uploaded = [];
        try {
            $p = DB::transaction(function () use ($r, &$uploaded) {
                $p = Product::create($this->data($r));
                $p->tags()->sync($r->input('tag_ids', []));
                $this->images($r, $p, $uploaded);
                $this->variants($r, $p, $uploaded);
                return $p;
            });
        } catch (\Throwable $e) {
            foreach ($uploaded as $path) app(OptimizedImageStorage::class)->delete($path);
            throw $e;
        }

        return redirect()->route('admin.products.edit', $p)->with('success', 'Product created.');
    }

    public function edit(Product $product)
    {
        $product->load('images', 'variants', 'tags');

        return view('admin.products.form', ['product' => $product, 'categories' => Category::orderBy('name')->get(), 'brands' => Brand::where('is_active', true)->orderBy('name')->get(), 'tags' => Tag::orderBy('name')->get()]);
    }

    public function update(Request $r, Product $product)
    {
        $uploaded = [];
        try {
            DB::transaction(function () use ($r, $product, &$uploaded) {
                $product->update($this->data($r, $product));
                $product->tags()->sync($r->input('tag_ids', []));
                $this->images($r, $product, $uploaded);
                $this->variants($r, $product, $uploaded);
            });
        } catch (\Throwable $e) {
            foreach ($uploaded as $path) app(OptimizedImageStorage::class)->delete($path);
            throw $e;
        }

        return back()->with('success', 'Product updated.');
    }

    public function destroy(Product $product)
    {
        DB::transaction(function () use ($product) {
            $imageStorage = app(OptimizedImageStorage::class);

            foreach ($product->images as $img) {
                $imageStorage->delete($img->image_path);
                $img->delete();
            }

            if ($product->image) {
                $imageStorage->delete($product->image);
            }

            foreach ($product->variants as $variant) {
                if ($variant->image) {
                    $imageStorage->delete($variant->image);
                }
                $variant->delete();
            }

            $product->tags()->detach();

            foreach ($product->reviews as $review) {
                if ($review->image_path) {
                    $imageStorage->delete($review->image_path);
                }
                $review->delete();
            }
            foreach ($product->legacyReviews as $legacyReview) {
                $legacyReview->delete();
            }

            $product->cartItems()->delete();
            $product->wishlists()->delete();
            $product->inventoryMovements()->delete();

            \App\Models\OrderItem::where('product_id', $product->id)->update([
                'product_id' => null,
                'variant_id' => null,
            ]);

            $product->delete();
        });

        return back()->with('success', 'Product and all associated media files permanently deleted.');
    }

    public function destroyImage(Product $product, ProductImage $image)
    {
        abort_unless($image->product_id === $product->id, 404);
        app(OptimizedImageStorage::class)->delete($image->image_path);
        $image->delete();

        return back()->with('success', 'Image removed.');
    }

    private function data(Request $r, ?Product $p = null)
    {
        $hasVariants = $r->boolean('has_variants');
        $d = $r->validate(['category_id' => 'required|exists:categories,id', 'brand_id' => 'nullable|exists:brands,id', 'tag_ids' => 'nullable|array', 'tag_ids.*' => 'integer|exists:tags,id', 'name' => 'required|string|max:255', 'slug' => ['required', 'alpha_dash', 'max:255', Rule::unique('products')->ignore($p?->id)], 'sku' => ['nullable', 'string', 'max:100', Rule::unique('products')->ignore($p?->id)], 'description' => 'nullable|string|max:50000', 'price' => 'required|numeric|min:0', 'discount_price' => 'nullable|numeric|min:0|lte:price', 'stock' => 'required|integer|min:0', 'badge_text' => 'nullable|string|max:30', 'sort_order' => 'required|integer|min:0', 'meta_title' => 'nullable|string|max:255', 'meta_description' => 'nullable|string|max:1000', 'has_variants' => 'nullable|boolean', 'is_active' => 'nullable|boolean', 'is_featured' => 'nullable|boolean', 'is_new' => 'nullable|boolean', 'is_preorder' => 'nullable|boolean',
            'variants' => [$hasVariants ? 'required' : 'nullable', 'array', $hasVariants ? 'min:1' : 'max:0'],
            'variants.*.id' => 'nullable|integer', 'variants.*.color_name' => 'required_with:variants|string|max:100',
            'variants.*.color_code' => ['required_with:variants', 'regex:/^#[0-9A-Fa-f]{6}$/'], 'variants.*.sku' => 'required_with:variants|string|max:100',
            'variants.*.regular_price' => 'required_with:variants|numeric|min:0', 'variants.*.sale_price' => 'nullable|numeric|min:0',
            'variants.*.stock' => 'required_with:variants|integer|min:0', 'variants.*.image' => 'nullable|image|mimes:png,webp,jpg,jpeg|max:5120',
            'variants.*.is_active' => 'nullable|boolean', 'variants.*.is_default' => 'nullable|boolean',
        ]);
        foreach (['has_variants', 'is_active', 'is_featured', 'is_new', 'is_preorder'] as $k) {
            $d[$k] = $r->boolean($k);
        }
        unset($d['variants']);

        return $d;
    }

    private function images(Request $r, Product $p, array &$uploaded = [])
    {
        $r->validate(['images.*' => 'image|mimes:png,webp,jpg,jpeg|max:5120']);
        foreach ($r->file('images', []) as $i => $file) {
            $path = app(OptimizedImageStorage::class)->store($file, 'products');
            $uploaded[] = $path;
            ProductImage::create(['product_id' => $p->id, 'image_path' => $path, 'alt_text' => $p->name, 'sort_order' => $p->images()->max('sort_order') + 1 + $i, 'is_primary' => $p->images()->doesntExist()]);
            if (! $p->image) {
                $p->update(['image' => $path]);
            }
        }
    }

    private function variants(Request $request, Product $product, array &$uploaded): void
    {
        if (! $request->boolean('has_variants')) return;
        $seen = [];
        $defaultSeen = false;
        foreach ($request->input('variants', []) as $index => $row) {
            $id = isset($row['id']) ? (int) $row['id'] : null;
            $variant = $id ? $product->variants()->findOrFail($id) : new ProductVariant(['product_id' => $product->id]);
            if (ProductVariant::where('sku', $row['sku'])->when($id, fn ($q) => $q->whereKeyNot($id))->exists()) {
                throw \Illuminate\Validation\ValidationException::withMessages(['variants.'.$index.'.sku' => 'This SKU is already in use.']);
            }
            if (filled($row['sale_price'] ?? null) && (float) $row['sale_price'] > (float) $row['regular_price']) {
                throw \Illuminate\Validation\ValidationException::withMessages(['variants.'.$index.'.sale_price' => 'Sale price cannot exceed regular price.']);
            }
            $isDefault = ! $defaultSeen && filter_var($row['is_default'] ?? false, FILTER_VALIDATE_BOOLEAN);
            $defaultSeen = $defaultSeen || $isDefault;
            $variant->fill(['name' => $row['color_name'], 'color_name' => $row['color_name'], 'color' => $row['color_name'], 'color_code' => $row['color_code'], 'sku' => $row['sku'], 'regular_price' => $row['regular_price'], 'sale_price' => filled($row['sale_price'] ?? null) ? $row['sale_price'] : null, 'price_adjustment' => 0, 'stock' => $row['stock'], 'is_active' => filter_var($row['is_active'] ?? false, FILTER_VALIDATE_BOOLEAN), 'is_default' => $isDefault, 'sort_order' => $index]);
            if ($file = $request->file('variants.'.$index.'.image')) {
                $path = app(OptimizedImageStorage::class)->store($file, 'variants', 1400);
                $uploaded[] = $path;
                $variant->image = $path;
            }
            $variant->save();
            $seen[] = $variant->id;
        }
        $product->variants()->whereNotIn('id', $seen)->update(['is_active' => false, 'is_default' => false]);
        if (! $defaultSeen && count($seen)) $product->variants()->whereKey($seen[0])->update(['is_default' => true]);
    }
}
