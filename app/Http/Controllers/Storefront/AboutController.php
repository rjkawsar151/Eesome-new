<?php
namespace App\Http\Controllers\Storefront;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Testimonial;
class AboutController extends Controller
{
    public function __invoke()
    {
        $testimonials = Testimonial::where('is_active', true)->orderBy('sort_order')->limit(3)->get();
        $aboutProducts = Product::with('images')->where('is_active', true)->where(function ($query) {
            $query->whereNotNull('image')->orWhereHas('images');
        })->orderByInStockFirst()->orderByDesc('is_featured')->orderBy('sort_order')->limit(8)->get();
        return view('storefront.about', compact('testimonials', 'aboutProducts'));
    }
}