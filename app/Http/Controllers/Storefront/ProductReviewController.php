<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductReviewController extends Controller
{
    public function store(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'customer_name' => [Rule::requiredIf(! $request->user()), 'nullable', 'string', 'max:100'],
            'email' => [Rule::requiredIf(! $request->user()), 'nullable', 'email', 'max:255'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'review_text' => ['required', 'string', 'min:10', 'max:2000'],
        ]);

        ProductReview::create([
            'product_id' => $product->id,
            'user_id' => $request->user()?->id,
            'customer_name' => $request->user()?->name ?? $validated['customer_name'],
            'email' => $request->user()?->email ?? $validated['email'],
            'rating' => $validated['rating'],
            'review_text' => $validated['review_text'],
            'status' => 'Pending',
        ]);

        return back()->with('success', 'Thank you. Your review was submitted for approval.');
    }
}
