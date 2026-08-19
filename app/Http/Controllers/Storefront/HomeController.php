<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\BlogPost;
use App\Services\SiteSettingsRepository;

class HomeController extends Controller
{
    public function __construct(private SiteSettingsRepository $settings) {}

    public function index()
    {
        $categories = Category::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'image']);

        $featuredProducts = Product::with(['images', 'category'])
            ->where('is_featured', true)
            ->where('is_active', true)
            ->orderByInStockFirst()
            ->orderBy('sort_order')
            ->get(['id', 'category_id', 'name', 'slug', 'price', 'discount_price', 'image',
                   'is_new', 'is_sold_out', 'is_preorder', 'badge_text', 'stock', 'is_featured', 'is_active']);

        $allProducts = Product::with(['images', 'activeVariants'])
            ->where('is_active', true)
            ->orderByInStockFirst()
            ->latest('id')
            ->paginate(12);

        $latestPosts = BlogPost::latest()->take(3)->get();

        return view('storefront.home', compact(
            'categories', 'featuredProducts', 'allProducts', 'latestPosts'
        ));
    }
}
