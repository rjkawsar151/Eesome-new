<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Services\ProductImageResolver;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(private ProductImageResolver $imageResolver) {}

    public function index(Request $request)
    {
        $query = Product::with(['images', 'category'])->where('is_active', true);

        if ($request->filled('category')) {
            $query->where('category_id', $request->integer('category'));
        }

        if ($request->filled('search')) {
            $search = trim($request->string('search')->toString());
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhereHas('category', fn ($category) => $category->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('images', fn ($image) => $image->where('color_name', 'like', "%{$search}%"));
            });
        }

        if ($request->boolean('new')) {
            $query->where('is_new', true);
        }

        if ($request->boolean('featured')) {
            $query->where('is_featured', true);
        }

        $randomSeed = (int) $request->session()->get('catalog_random_seed');
        if ($randomSeed === 0) {
            $randomSeed = random_int(1, 2147483647);
            $request->session()->put('catalog_random_seed', $randomSeed);
        }

        if ($query->getConnection()->getDriverName() === 'mysql') {
            $query->orderByRaw('RAND(?)', [$randomSeed]);
        } else {
            $query->inRandomOrder();
        }

        $products = $query
            ->paginate(8)
            ->withQueryString();

        $categories = Category::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
        $wishlistIds = auth()->check()
            ? \App\Models\Wishlist::where('user_id', auth()->id())->pluck('product_id')->all()
            : [];

        return view('storefront.products.index', compact('products', 'categories', 'wishlistIds'));
    }

    public function show(string $slug)
    {
        $product = Product::with(['category', 'images'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $reviews = $product->legacyReviews()
            ->with('user:id,name')
            ->whereRaw('LOWER(status) = ?', ['approved'])
            ->latest()
            ->paginate(8);

        $avgRating = $product->legacyReviews()->whereRaw('LOWER(status) = ?', ['approved'])->avg('rating');
        $reviewCount = $product->legacyReviews()->whereRaw('LOWER(status) = ?', ['approved'])->count();

        $relatedProducts = Product::with('images')
            ->where('category_id', $product->category_id)
            ->where('is_active', true)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        return view('storefront.products.show', compact(
            'product', 'reviews', 'avgRating', 'reviewCount', 'relatedProducts'
        ));
    }
}
