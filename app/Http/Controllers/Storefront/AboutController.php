<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;

class AboutController extends Controller
{
    public function __invoke()
    {
        $testimonials = Testimonial::where('is_active', true)->orderBy('sort_order')->get();

        return view('storefront.about', compact('testimonials'));
    }
}
