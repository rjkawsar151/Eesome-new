@extends('layouts.app')
@section('title', $product->meta_title ?: $product->name)
@section('meta_description', $product->meta_description ?: \Illuminate\Support\Str::limit(strip_tags($product->description), 155))
@push('styles')
<style>
.detail{display:grid;grid-template-columns:1.05fr .95fr;gap:3rem;padding-top:3rem;min-width:0}.detail>div{min-width:0}.main-image{width:100%;aspect-ratio:1;object-fit:cover;border-radius:24px;background:var(--brand-50)}.thumbs{display:flex;gap:.65rem;margin-top:.75rem;overflow:auto}.thumbs button{padding:0;border:2px solid var(--brand-100);border-radius:10px;background:#fff;cursor:pointer}.thumbs img{display:block;width:74px;height:74px;object-fit:cover;border-radius:8px}.eyebrow{color:var(--brand-700);font-weight:700}.detail h1{font-size:clamp(2rem,4vw,3.2rem);margin:.5rem 0}.detail-price{font-size:1.7rem;color:var(--brand-700);font-weight:800}.detail-price s{font-size:1rem;color:#9ca3af;margin-left:.5rem}.stock{display:inline-block;padding:.35rem .7rem;border-radius:999px;background:#dcfce7;color:#166534;font-weight:700;font-size:.8rem}.description{line-height:1.8;color:#4b5563;overflow-wrap:anywhere;word-break:break-word}.variants{margin:1.25rem 0}.variant-list{display:flex;gap:.65rem;flex-wrap:wrap}.variant{border:1px solid var(--brand-100);border-radius:999px;background:#fff;padding:.5rem .8rem;cursor:pointer}.variant.active{border-color:var(--brand-600);background:var(--brand-50);color:var(--brand-700)}.buybox{display:flex;gap:.75rem;margin:1.5rem 0}.buybox input[type=number]{width:80px;border:1px solid #d1d5db;border-radius:10px;padding:.75rem}.buybox button{border:0;border-radius:10px;background:var(--brand-600);color:#fff;font-weight:800;padding:.8rem 1.5rem}.reviews,.related{margin-top:4rem}.review{border-top:1px solid #eee;padding:1rem 0}.stars{color:#f59e0b}.review-form{max-width:680px;display:grid;gap:.85rem;padding:1.25rem;margin:1.5rem 0 2rem;background:var(--brand-50);border-radius:16px}.review-form input,.review-form select,.review-form textarea{width:100%;padding:.75rem;border:1px solid #d1d5db;border-radius:9px;background:#fff}.review-form textarea{min-height:120px;resize:vertical}.review-form button{justify-self:start;border:0;border-radius:9px;background:var(--brand-600);color:#fff;padding:.75rem 1.2rem;font-weight:700;cursor:pointer}.review-fields{display:grid;grid-template-columns:1fr 1fr;gap:.85rem}.related-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:1rem}.related-grid img{width:100%;aspect-ratio:1;object-fit:cover;border-radius:14px}
@media(max-width:760px){.detail{grid-template-columns:1fr;gap:1.5rem}.review-fields,.related-grid{grid-template-columns:1fr}}
</style>
@endpush
@section('content')
<main class="container">
<section class="detail">
    <div>
        @php($primary = $product->images->first()?->image_path ?? $product->image)
        <img id="main-product-image" class="main-image" src="{{ app(\App\Services\ProductImageResolver::class)->resolve($primary) }}" onerror="this.onerror=null;this.src='{{ app(\App\Services\ProductImageResolver::class)->placeholder() }}'" alt="{{ $product->name }}">
        @if($product->images->count() > 1)
        <div class="thumbs">@foreach($product->images as $image)<button type="button" class="js-variant-image" data-image="{{ app(\App\Services\ProductImageResolver::class)->resolve($image->image_path) }}" data-color="{{ trim((string)$image->color_name) }}"><img src="{{ app(\App\Services\ProductImageResolver::class)->resolve($image->image_path) }}" alt="{{ $image->color_name ?: ($image->alt_text ?: $product->name) }}" loading="lazy"></button>@endforeach</div>
        @endif
    </div>
    <div>
        <span class="eyebrow">{{ $product->category?->name ?? 'EEsome Collection' }}</span>
        <h1>{{ $product->name }}</h1>
        <p style="color:var(--text-muted)">SKU: {{ $product->sku ?: 'N/A' }}</p>
        <div class="detail-price">৳{{ number_format((float)$product->effective_price, 0) }} @if($product->has_discount)<s>৳{{ number_format((float)$product->price, 0) }}</s>@endif</div>
        <p><span class="stock">{{ $product->available_for_preorder ? 'Available for preorder · delivery in 25–35 days' : ($product->stock > 0 ? $product->stock.' in stock' : 'Sold out') }}</span></p>
        @php($colorVariants = $product->images->filter(fn($image) => filled(trim((string)$image->color_name)))->unique(fn($image) => strtolower(trim($image->color_name))))
        @if($colorVariants->count())
        <div class="variants"><strong>Color: <span id="selected-color">{{ trim($colorVariants->first()->color_name) }}</span></strong><div class="variant-list">@foreach($colorVariants as $variant)<button type="button" class="variant js-variant-image @if($loop->first) active @endif" data-image="{{ app(\App\Services\ProductImageResolver::class)->resolve($variant->image_path) }}" data-color="{{ trim($variant->color_name) }}">{{ trim($variant->color_name) }}</button>@endforeach</div></div>
        @endif
        <div class="description">{!! $product->clean_description !!}</div>
        @if($product->stock > 0 || $product->available_for_preorder)
        <form class="buybox" method="POST" action="{{ route('cart.store') }}">@csrf<input type="hidden" name="product_id" value="{{ $product->id }}"><input type="hidden" id="selected-color-input" name="color" value="{{ trim((string)$colorVariants->first()?->color_name) }}"><input type="number" name="quantity" value="1" min="1" max="{{ $product->available_for_preorder ? 100 : max(1,$product->stock) }}" aria-label="Quantity"><button type="submit">Add to cart</button></form>
        @endif
    </div>
</section>
<section class="reviews">
    <h2>Customer reviews</h2>
    <p><span class="stars">{{ str_repeat('★', (int)round($avgRating ?? 0)) }}{{ str_repeat('☆', 5-(int)round($avgRating ?? 0)) }}</span> {{ number_format((float)($avgRating ?? 0), 1) }} from {{ $reviewCount }} reviews</p>
    <form class="review-form" method="POST" action="{{ route('products.reviews.store', $product) }}">@csrf
        <h3 style="margin:0">Write a review</h3>
        @guest<div class="review-fields"><label>Your name<input name="customer_name" value="{{ old('customer_name') }}" required></label><label>Email<input type="email" name="email" value="{{ old('email') }}" required></label></div>@endguest
        <label>Rating<select name="rating" required><option value="">Choose a rating</option>@for($rating=5;$rating>=1;$rating--)<option value="{{ $rating }}" @selected(old('rating')==$rating)>{{ $rating }} star{{ $rating > 1 ? 's' : '' }}</option>@endfor</select></label>
        <label>Your review<textarea name="review_text" minlength="10" maxlength="2000" required>{{ old('review_text') }}</textarea></label>
        @if($errors->any())<div class="alert alert-error">{{ $errors->first() }}</div>@endif
        <button type="submit">Submit review</button>
    </form>
    @forelse($reviews as $review)<article class="review"><strong>{{ $review->user?->name ?? $review->customer_name ?? 'Customer' }}</strong><div class="stars">{{ str_repeat('★',$review->rating) }}{{ str_repeat('☆',5-$review->rating) }}</div><p>{{ $review->review_text }}</p></article>@empty<p>No approved reviews yet.</p>@endforelse
    {{ $reviews->links() }}
</section>
@if($relatedProducts->count())<section class="related"><h2>You may also like</h2><div class="related-grid">@foreach($relatedProducts as $related)@php($img=$related->images->first()?->image_path ?? $related->image)<a href="{{ route('products.show',$related->slug ?? $related->id) }}" style="text-decoration:none;color:inherit"><img src="{{ app(\App\Services\ProductImageResolver::class)->resolve($img) }}" alt="{{ $related->name }}" loading="lazy"><strong>{{ $related->name }}</strong><div style="color:var(--brand-700)">৳{{ number_format((float)$related->effective_price,0) }}</div></a>@endforeach</div></section>@endif
</main>
@endsection
@push('scripts')
<script>
document.querySelectorAll('.js-variant-image').forEach((button) => {
    button.addEventListener('click', () => {
        document.getElementById('main-product-image').src = button.dataset.image;
        if (button.dataset.color) {
            const label = document.getElementById('selected-color');
            const input = document.getElementById('selected-color-input');
            if (label) label.textContent = button.dataset.color;
            if (input) input.value = button.dataset.color;
            document.querySelectorAll('.variant').forEach((variant) => variant.classList.toggle('active', variant.dataset.color === button.dataset.color));
        }
    });
});
</script>
@endpush
