<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
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
        $p = DB::transaction(fn () => Product::create($this->data($r)));
        $p->tags()->sync($r->input('tag_ids', []));
        $this->images($r, $p);

        return redirect()->route('admin.products.edit', $p)->with('success', 'Product created.');
    }

    public function edit(Product $product)
    {
        $product->load('images', 'variants', 'tags');

        return view('admin.products.form', ['product' => $product, 'categories' => Category::orderBy('name')->get(), 'brands' => Brand::where('is_active', true)->orderBy('name')->get(), 'tags' => Tag::orderBy('name')->get()]);
    }

    public function update(Request $r, Product $product)
    {
        $product->update($this->data($r, $product));
        $product->tags()->sync($r->input('tag_ids', []));
        $this->images($r, $product);

        return back()->with('success', 'Product updated.');
    }

    public function destroy(Product $product)
    {
        $product->update(['is_active' => false]);

        return back()->with('success', 'Product archived.');
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
        $d = $r->validate(['category_id' => 'required|exists:categories,id', 'brand_id' => 'nullable|exists:brands,id', 'tag_ids' => 'nullable|array', 'tag_ids.*' => 'integer|exists:tags,id', 'name' => 'required|string|max:255', 'slug' => ['required', 'alpha_dash', 'max:255', Rule::unique('products')->ignore($p?->id)], 'sku' => ['nullable', 'string', 'max:100', Rule::unique('products')->ignore($p?->id)], 'description' => 'nullable|string|max:50000', 'price' => 'required|numeric|min:0', 'discount_price' => 'nullable|numeric|min:0', 'stock' => 'required|integer|min:0', 'badge_text' => 'nullable|string|max:30', 'sort_order' => 'required|integer|min:0', 'meta_title' => 'nullable|string|max:255', 'meta_description' => 'nullable|string|max:1000', 'is_active' => 'nullable|boolean', 'is_featured' => 'nullable|boolean', 'is_new' => 'nullable|boolean', 'is_preorder' => 'nullable|boolean']);
        foreach (['is_active', 'is_featured', 'is_new', 'is_preorder'] as $k) {
            $d[$k] = $r->boolean($k);
        }

        return $d;
    }

    private function images(Request $r, Product $p)
    {
        $r->validate(['images.*' => 'image|mimes:png,webp,jpg,jpeg|max:5120']);
        foreach ($r->file('images', []) as $i => $file) {
            $path = app(OptimizedImageStorage::class)->store($file, 'products');
            ProductImage::create(['product_id' => $p->id, 'image_path' => $path, 'alt_text' => $p->name, 'sort_order' => $p->images()->max('sort_order') + 1 + $i, 'is_primary' => $p->images()->doesntExist()]);
            if (! $p->image) {
                $p->update(['image' => $path]);
            }
        }
    }
}
