<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Response;

class FacebookFeedController extends Controller
{
    /**
     * Generate Meta / Facebook Catalog Product XML Feed.
     */
    public function index(): Response
    {
        $products = Product::with(['category', 'brand', 'images', 'activeVariants'])
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $xml = view('feeds.facebook', compact('products'))->render();

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
