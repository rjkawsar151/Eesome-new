@extends('layouts.app')
@section('title', $product->meta_title ?: $product->name)
@section('meta_description', $product->meta_description ?: \Illuminate\Support\Str::limit(strip_tags($product->description), 155))
@push('styles')
<style>
.detail{display:grid;grid-template-columns:1.05fr .95fr;gap:3rem;padding-top:3rem;min-width:0}.detail>div{min-width:0}
.main-image-wrap{position:relative;width:100%;aspect-ratio:1;border-radius:24px;overflow:hidden;background:var(--brand-50);display:flex;align-items:center;justify-content:center}
.main-image{width:100%;height:100%;object-fit:contain;transition:opacity .2s ease}
.main-image-badge{position:absolute;top:1rem;left:1rem;z-index:10;background:rgba(26,10,46,.85);color:#fff;padding:.45rem .95rem;border-radius:999px;font-size:.85rem;font-weight:700;letter-spacing:.02em;box-shadow:0 4px 16px rgba(0,0,0,.22);backdrop-filter:blur(8px);pointer-events:none;transition:all .2s ease}
.main-image-badge:empty{display:none}
.thumbs{display:flex;gap:.65rem;margin-top:.75rem;overflow:auto}.thumbs button{padding:0;border:2px solid var(--brand-100);border-radius:10px;background:#fff;cursor:pointer;transition:border-color .2s,box-shadow .2s;flex-shrink:0}.thumbs button.active{border-color:var(--brand-600);box-shadow:0 0 0 2px var(--brand-100)}.thumbs img{display:block;width:74px;height:74px;object-fit:contain;border-radius:8px}.eyebrow{color:var(--brand-700);font-weight:700}.detail h1{font-size:clamp(2rem,4vw,3.2rem);margin:.5rem 0}.detail-price{font-size:1.7rem;color:var(--brand-700);font-weight:800}.detail-price s{font-size:1rem;color:#9ca3af;margin-left:.5rem}.stock{display:inline-block;padding:.35rem .7rem;border-radius:999px;background:#dcfce7;color:#166534;font-weight:700;font-size:.8rem}.description{line-height:1.8;color:#4b5563;overflow-wrap:anywhere}.variants{margin:1.25rem 0}.variant-list{display:flex;gap:.65rem;flex-wrap:wrap}.variant{display:inline-flex;align-items:center;gap:.45rem;border:1px solid var(--brand-100);border-radius:999px;background:#fff;padding:.5rem .8rem;cursor:pointer;font:inherit;transition:all .15s ease}.variant.active{border-color:var(--brand-600);background:var(--brand-50);color:var(--brand-700);font-weight:700}.variant:disabled{opacity:.5;cursor:not-allowed}.color-swatch-dot{width:14px;height:14px;border-radius:50%;border:1px solid rgba(0,0,0,.15);display:inline-block;flex-shrink:0}.buybox{display:flex;gap:.75rem;margin:1.5rem 0}.buybox input[type=number]{width:80px;border:1px solid #d1d5db;border-radius:10px;padding:.75rem}.buybox button{border:0;border-radius:10px;background:var(--brand-600);color:#fff;font-weight:800;padding:.8rem 1.5rem;cursor:pointer}.reviews,.review-entry,.related{margin-top:4rem}.review{border-top:1px solid #eee;padding:1.25rem 0}.stars{color:#f59e0b}.review-image{display:block;max-width:320px;max-height:320px;width:auto;height:auto;object-fit:contain;border-radius:14px;border:1px solid var(--brand-100);margin-top:.75rem}.review-form{max-width:760px;display:grid;gap:.85rem;padding:1.25rem;margin:1.5rem 0 2rem;background:var(--brand-50);border-radius:16px}.review-form input,.review-form select,.review-form textarea{width:100%;padding:.75rem;border:1px solid #d1d5db;border-radius:9px;background:#fff}.review-form textarea{min-height:120px;resize:vertical}.review-form button{justify-self:start;border:0;border-radius:9px;background:var(--brand-600);color:#fff;padding:.75rem 1.2rem;font-weight:700;cursor:pointer}.review-fields{display:grid;grid-template-columns:1fr 1fr;gap:.85rem}.related-header{display:flex;align-items:end;justify-content:space-between;gap:1rem;margin-bottom:1.25rem}.related-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:1.25rem}.related-card{display:flex;flex-direction:column;min-width:0;border:1px solid #f0e4eb;border-radius:18px;background:#fff;overflow:hidden;box-shadow:0 4px 16px rgba(23,18,26,.05)}.related-card__image{display:block;width:100%;aspect-ratio:1/1;object-fit:contain;background:var(--brand-50);padding:.5rem}.related-card__body{display:flex;flex:1;flex-direction:column;padding:.9rem}.related-card__name{margin:0 0 .4rem;font-size:.95rem;line-height:1.35}.related-card__price{color:var(--brand-700);font-weight:800;margin-bottom:.8rem}.related-card__actions{display:grid;grid-template-columns:1fr 1fr;gap:.5rem;margin-top:auto}.related-card__actions form{margin:0}.related-card__actions button,.related-card__actions a{display:flex;width:100%;min-height:40px;align-items:center;justify-content:center;border-radius:9px;padding:.55rem;border:0;font:inherit;font-size:.78rem;font-weight:800;text-decoration:none;cursor:pointer}.related-card__cart{background:var(--brand-600);color:#fff}.related-card__details{background:var(--brand-50);color:var(--brand-700);border:1px solid var(--brand-100)!important}.explore-all{display:inline-flex;align-items:center;justify-content:center;border-radius:999px;padding:.7rem 1.1rem;background:var(--brand-600);color:#fff;text-decoration:none;font-weight:800;white-space:nowrap}
@media(max-width:980px){.related-grid{grid-template-columns:repeat(3,minmax(0,1fr))}}
@media(max-width:760px){.detail{grid-template-columns:1fr;gap:1.5rem}.review-fields{grid-template-columns:1fr}.related-grid{grid-template-columns:repeat(2,minmax(0,1fr));gap:.75rem}.related-header{align-items:flex-start;flex-direction:column}.related-card__actions{grid-template-columns:1fr}.review-image{max-width:100%}}
@keyframes variant-dialog-in{from{opacity:0;transform:translateY(16px) scale(.975)}to{opacity:1;transform:translateY(0) scale(1)}}@keyframes variant-backdrop-in{from{background:rgba(23,18,26,0)}to{background:rgba(23,18,26,.55)}}.variant-dialog[open]{animation:variant-dialog-in 280ms cubic-bezier(.22,1,.36,1) both}.variant-dialog[open]::backdrop{animation:variant-backdrop-in 240ms ease-out both}.variant-dialog{width:min(92vw,480px);padding:1.5rem;border:0;border-radius:22px;box-shadow:0 24px 70px rgba(25,15,22,.28);text-align:center}.variant-dialog::backdrop{background:rgba(23,18,26,.55);backdrop-filter:blur(3px)}.variant-close{position:absolute;right:.8rem;top:.8rem;width:40px;height:40px;border:0;border-radius:50%;background:var(--brand-50);font-size:1.35rem;cursor:pointer}.variant-dialog-image{width:150px;height:150px;margin:auto;object-fit:contain}.variant-select{width:100%;margin:.75rem 0;padding:.8rem;border:1px solid var(--brand-100);border-radius:10px}.variant-feedback{color:var(--text-muted);font-size:.85rem}.variant-dialog #variant-confirm{width:100%;min-height:46px;border:0;border-radius:10px;background:var(--brand-600);color:#fff;font-weight:800;cursor:pointer}@media(max-width:600px){@keyframes variant-dialog-in{from{opacity:0;transform:translateY(42px)}to{opacity:1;transform:translateY(0)}}.variant-dialog{width:100%;max-width:none;margin:auto 0 0;border-radius:22px 22px 0 0}}@media(prefers-reduced-motion:reduce){.variant-dialog[open],.variant-dialog[open]::backdrop{animation:none}}
</style>
@endpush
@section('content')
@php
    $hasActiveVariants = $product->has_variants && $product->activeVariants->isNotEmpty();
    $colorVariants = $product->images->filter(fn($image) => filled(trim((string)$image->color_name)))->unique(fn($image) => strtolower(trim($image->color_name)));
    $defaultVariant = $hasActiveVariants ? ($product->activeVariants->firstWhere('is_default', true) ?? $product->activeVariants->first()) : null;

    $resolveVariantImage = function($variant, $index = 0) use ($product) {
        if (!$variant) return null;
        $colorName = trim($variant->color_name ?: $variant->name);
        $imgPath = $variant->image;
        if (!$imgPath) {
            $matched = $product->images->first(function($img) use ($colorName) {
                return (filled($img->color_name) && strcasecmp(trim($img->color_name), $colorName) === 0)
                    || (filled($img->alt_text) && strcasecmp(trim($img->alt_text), $colorName) === 0);
            });
            $imgPath = $matched?->image_path;
        }
        if (!$imgPath && isset($product->images[$index]) && $product->images->count() >= $product->activeVariants->count()) {
            $imgPath = $product->images[$index]->image_path;
        }
        if (!$imgPath) {
            $imgPath = $product->images->first()?->image_path ?? $product->image;
        }
        return $imgPath ? app(\App\Services\ProductImageResolver::class)->resolve($imgPath) : null;
    };

    $initialResolvedImage = $defaultVariant ? $resolveVariantImage($defaultVariant, 0) : null;
    if (!$initialResolvedImage) {
        $primary = $product->images->first()?->image_path ?? $product->image;
        $initialResolvedImage = app(\App\Services\ProductImageResolver::class)->resolve($primary);
    }
    $initialColorName = trim($defaultVariant?->color_name ?: ($defaultVariant?->name ?: ($colorVariants->first()?->color_name ?? '')));
@endphp
<main class="container">
<section class="detail">
    <div>
        <div class="main-image-wrap">
            <span id="main-image-badge" class="main-image-badge" @if(empty($initialColorName)) style="display:none" @endif>{{ $initialColorName }}</span>
            <img id="main-product-image" class="main-image" src="{{ $initialResolvedImage }}" onerror="this.onerror=null;this.src='{{ app(\App\Services\ProductImageResolver::class)->placeholder() }}'" alt="{{ $product->name }}">
        </div>
        @if($product->images->count() > 1)
        <div class="thumbs">
            @foreach($product->images as $image)
                @php($resolvedThumb = app(\App\Services\ProductImageResolver::class)->resolve($image->image_path))
                <button type="button" class="js-variant-image @if($resolvedThumb === $initialResolvedImage || ($loop->first && !$defaultVariant)) active @endif" onclick="handleThumbClick(this, event)" data-image="{{ $resolvedThumb }}" data-color="{{ trim((string)$image->color_name) }}" title="{{ $image->color_name ?: ($image->alt_text ?: $product->name) }}">
                    <img src="{{ $resolvedThumb }}" onerror="this.onerror=null;this.src='{{ app(\App\Services\ProductImageResolver::class)->placeholder() }}'" alt="{{ $image->color_name ?: ($image->alt_text ?: $product->name) }}" loading="lazy">
                </button>
            @endforeach
        </div>
        @endif
    </div>
    <div>
        <span class="eyebrow">{{ $product->category?->name ?? 'EEsome Collection' }}</span>
        <h1>{{ $product->name }}</h1>
        <p style="color:var(--text-muted)">SKU: <span id="product-sku">{{ $defaultVariant?->sku ?: ($product->sku ?: 'N/A') }}</span></p>
        <div class="detail-price">৳{{ number_format((float)($defaultVariant ? $defaultVariant->effective_price : $product->effective_price), 0) }} @if($product->has_discount || ($defaultVariant && $defaultVariant->sale_price !== null && (float)$defaultVariant->sale_price < (float)$defaultVariant->regular_price))<s>৳{{ number_format((float)($defaultVariant ? $defaultVariant->regular_price : $product->price), 0) }}</s>@endif</div>
        <p><span class="stock">{{ $product->available_for_preorder ? 'Available for preorder · delivery in 25–35 days' : (($defaultVariant?->stock ?? $product->stock) > 0 ? ($defaultVariant?->stock ?? $product->stock).' in stock' : 'Sold out') }}</span></p>
        
        @if($hasActiveVariants)
        <div class="variants">
            <strong>Color: <span id="selected-color">{{ $initialColorName }}</span></strong>
            <div class="variant-list">
                @foreach($product->activeVariants as $index => $variant)
                    @php($varImg = $resolveVariantImage($variant, $index))
                    @php($isActive = $defaultVariant && $defaultVariant->id === $variant->id)
                    <button type="button" class="variant js-variant-item @if($isActive) active @endif" onclick="handleVariantClick(this, event)" data-variant-id="{{ $variant->id }}" data-color="{{ trim($variant->color_name ?: $variant->name) }}" data-price="৳{{ number_format((float)$variant->effective_price, 0) }}" data-sku="{{ $variant->sku }}" @if($varImg) data-image="{{ $varImg }}" @endif @disabled($variant->stock < 1)>
                        @if($variant->color_code)
                            <span class="color-swatch-dot" style="background-color: {{ $variant->color_code }}"></span>
                        @endif
                        {{ trim($variant->color_name ?: $variant->name) }}
                    </button>
                @endforeach
            </div>
        </div>
        @elseif($colorVariants->count())
        <div class="variants">
            <strong>Color: <span id="selected-color">{{ trim($colorVariants->first()->color_name) }}</span></strong>
            <div class="variant-list">
                @foreach($colorVariants as $variant)
                    <button type="button" class="variant js-variant-image @if($loop->first) active @endif" onclick="handleColorFallbackClick(this, event)" data-image="{{ app(\App\Services\ProductImageResolver::class)->resolve($variant->image_path) }}" data-color="{{ trim($variant->color_name) }}">
                        {{ trim($variant->color_name) }}
                    </button>
                @endforeach
            </div>
        </div>
        @endif

        @if($product->has_variants && $product->activeVariants->count() > 1)
        <dialog id="variant-dialog" class="variant-dialog" aria-labelledby="variant-dialog-title">
            <button id="variant-close" class="variant-close" type="button" aria-label="Close color selector">×</button>
            <h2 id="variant-dialog-title">Choose your color</h2>
            <img class="variant-dialog-image" src="{{ $initialResolvedImage }}" alt="{{ $product->name }}">
            <p>{{ $product->name }}</p>
            <label for="purchase-variant"><strong>Available colors</strong></label>
            <select id="purchase-variant" class="variant-select">
                <option value="">Choose a color</option>
                @foreach($product->activeVariants as $index => $variant)
                    @php($varImg = $resolveVariantImage($variant, $index))
                    <option value="{{ $variant->id }}" data-image="{{ $varImg }}" data-color="{{ trim($variant->color_name ?: $variant->name) }}" @selected($defaultVariant && $defaultVariant->id === $variant->id) @disabled($variant->stock < 1)>{{ $variant->color_name ?: $variant->name }} / SKU {{ $variant->sku }} / BDT {{ number_format((float)$variant->effective_price,0) }}</option>
                @endforeach
            </select>
            <p id="variant-feedback" class="variant-feedback">Select an available color to continue.</p>
            <button id="variant-confirm" type="button">Continue</button>
        </dialog>
        @endif

        <div class="description">{!! $product->clean_description !!}</div>
        @if($product->stock > 0 || $product->available_for_preorder)
        <form id="purchase-form" class="buybox" method="POST" action="{{ route('cart.store') }}">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            @if($hasActiveVariants)
                <input type="hidden" id="selected-variant-id" name="variant_id" value="{{ $defaultVariant?->id ?? '' }}">
            @endif
            <input type="number" name="quantity" value="1" min="1" max="{{ $product->available_for_preorder ? 100 : max(1, $defaultVariant?->stock ?? $product->stock) }}" aria-label="Quantity">
            <button type="submit">Add to cart</button>
            <button type="submit" name="buy_now" value="1">Buy now</button>
        </form>
        @endif
    </div>
</section>

<section class="reviews">
    <h2>Customer reviews</h2>
    <p><span class="stars">{{ str_repeat('★', (int)round($avgRating ?? 0)) }}{{ str_repeat('☆', 5-(int)round($avgRating ?? 0)) }}</span> {{ number_format((float)($avgRating ?? 0), 1) }} from {{ $reviewCount }} reviews</p>
    @forelse($reviews as $review)
        <article class="review">
            <strong>{{ $review->user?->name ?? $review->customer_name ?? 'Customer' }}</strong>
            <div class="stars">{{ str_repeat('★',$review->rating) }}{{ str_repeat('☆',5-$review->rating) }}</div>
            <p>{{ $review->review_text }}</p>
            @if($review->image_path)<img class="review-image" src="{{ asset('storage/'.$review->image_path) }}" alt="Review photo from {{ $review->user?->name ?? $review->customer_name ?? 'customer' }}" loading="lazy">@endif
        </article>
    @empty
        <p>No approved reviews yet.</p>
    @endforelse
    {{ $reviews->links() }}
</section>

<section class="review-entry">
    <h2>Share your experience</h2>
    <form class="review-form" method="POST" enctype="multipart/form-data" action="{{ route('products.reviews.store', $product) }}">@csrf
        <h3 style="margin:0">Write a review</h3>
        @guest<div class="review-fields"><label>Your name<input name="customer_name" value="{{ old('customer_name') }}" required></label><label>Email<input type="email" name="email" value="{{ old('email') }}" required></label></div>@endguest
        <label>Rating<select name="rating" required><option value="">Choose a rating</option>@for($rating=5;$rating>=1;$rating--)<option value="{{ $rating }}" @selected(old('rating')==$rating)>{{ $rating }} star{{ $rating > 1 ? 's' : '' }}</option>@endfor</select></label>
        <label>Your review<textarea name="review_text" minlength="10" maxlength="2000" required>{{ old('review_text') }}</textarea></label>
        <label>Review image (optional)<input type="file" name="review_image" accept="image/png,image/webp,image/jpeg"><small>PNG, WebP, or JPG. Maximum 3MB.</small></label>
        @if($errors->any())<div class="alert alert-error">{{ $errors->first() }}</div>@endif
        <button type="submit">Submit review</button>
    </form>
</section>

@if($relatedProducts->count())
<section class="related">
    <div class="related-header"><div><h2 style="margin:0">You may also like</h2><p style="color:var(--text-muted)">More pieces selected for you</p></div><a class="explore-all" href="{{ route('products.index') }}">Explore all products</a></div>
    <div class="related-grid">
        @foreach($relatedProducts as $related)
            @php($img=$related->images->first()?->image_path ?? $related->image)
            <article class="related-card">
                <a href="{{ route('products.show',$related->slug ?? $related->id) }}"><img class="related-card__image" src="{{ app(\App\Services\ProductImageResolver::class)->resolve($img) }}" onerror="this.onerror=null;this.src='{{ app(\App\Services\ProductImageResolver::class)->placeholder() }}'" alt="{{ $related->name }}" loading="lazy"></a>
                <div class="related-card__body"><h3 class="related-card__name">{{ $related->name }}</h3><div class="related-card__price">৳{{ number_format((float)$related->effective_price,0) }}</div><div class="related-card__actions">
                    @if($related->stock > 0 || $related->available_for_preorder)<form method="POST" action="{{ route('cart.store') }}">@csrf<input type="hidden" name="product_id" value="{{ $related->id }}"><input type="hidden" name="quantity" value="1"><button class="related-card__cart" type="submit">Add to cart</button></form>@else<button class="related-card__cart" type="button" disabled>Sold out</button>@endif
                    <a class="related-card__details" href="{{ route('products.show',$related->slug ?? $related->id) }}">Details</a>
                </div></div>
            </article>
        @endforeach
    </div>
</section>
@endif
</main>
@endsection
@push('scripts')
<script>
window.handleVariantClick = function(button, event) {
    if (!button || button.disabled) return;

    document.querySelectorAll('.js-variant-item').forEach((v) => v.classList.remove('active'));
    button.classList.add('active');

    const variantId = button.getAttribute('data-variant-id') || button.dataset.variantId;
    const color = button.getAttribute('data-color') || button.dataset.color || '';
    const imgUrl = button.getAttribute('data-image') || button.dataset.image;
    const price = button.getAttribute('data-price') || button.dataset.price;
    const sku = button.getAttribute('data-sku') || button.dataset.sku;

    // 1. Replace main product image
    const mainImg = document.getElementById('main-product-image');
    if (mainImg && imgUrl) {
        mainImg.src = imgUrl;
    }

    // 2. Show variant name at left corner of image
    const imageBadge = document.getElementById('main-image-badge');
    if (imageBadge) {
        imageBadge.textContent = color;
        imageBadge.style.display = color ? 'inline-block' : 'none';
    }

    // 3. Update "Color: <selected-color>" text
    const colorSpan = document.getElementById('selected-color');
    if (colorSpan) {
        colorSpan.textContent = color;
    }

    // 4. Update hidden input for cart submission
    const variantInput = document.getElementById('selected-variant-id');
    if (variantInput && variantId) {
        variantInput.value = variantId;
    }

    // 5. Update purchase modal select if present
    const select = document.getElementById('purchase-variant');
    if (select && variantId) {
        select.value = variantId;
    }

    // 6. Update SKU
    const skuEl = document.getElementById('product-sku');
    if (skuEl && sku) {
        skuEl.textContent = sku;
    }

    // 7. Update Price
    if (price) {
        const priceEl = document.querySelector('.detail-price');
        if (priceEl) {
            const sTag = priceEl.querySelector('s');
            if (sTag) {
                priceEl.childNodes[0].nodeValue = price + ' ';
            } else {
                priceEl.textContent = price;
            }
        }
    }

    // 8. Sync Thumbnails
    const colorLower = color.trim().toLowerCase();
    let thumbMatched = false;
    document.querySelectorAll('.thumbs .js-variant-image').forEach((thumb) => {
        const thumbColor = (thumb.getAttribute('data-color') || thumb.dataset.color || '').trim().toLowerCase();
        const thumbImg = thumb.getAttribute('data-image') || thumb.dataset.image || '';
        const isMatch = (colorLower && thumbColor && thumbColor === colorLower) || (imgUrl && thumbImg === imgUrl);
        if (isMatch && !thumbMatched) {
            thumb.classList.add('active');
            thumbMatched = true;
            thumb.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
        } else {
            thumb.classList.remove('active');
        }
    });
};

window.handleThumbClick = function(thumb, event) {
    if (!thumb) return;

    document.querySelectorAll('.thumbs .js-variant-image').forEach((t) => t.classList.remove('active'));
    thumb.classList.add('active');

    const imgUrl = thumb.getAttribute('data-image') || thumb.dataset.image;
    const color = thumb.getAttribute('data-color') || thumb.dataset.color || '';

    const mainImg = document.getElementById('main-product-image');
    if (mainImg && imgUrl) {
        mainImg.src = imgUrl;
    }

    const imageBadge = document.getElementById('main-image-badge');
    if (imageBadge) {
        imageBadge.textContent = color;
        imageBadge.style.display = color ? 'inline-block' : 'none';
    }

    if (color) {
        const colorLower = color.trim().toLowerCase();
        const matchingBtn = Array.from(document.querySelectorAll('.js-variant-item')).find((btn) => {
            const c = (btn.getAttribute('data-color') || btn.dataset.color || '').trim().toLowerCase();
            return c === colorLower;
        });
        if (matchingBtn) {
            window.handleVariantClick(matchingBtn, event);
        } else {
            const colorSpan = document.getElementById('selected-color');
            if (colorSpan) colorSpan.textContent = color;
        }
    }
};

window.handleColorFallbackClick = function(btn, event) {
    if (!btn) return;
    document.querySelectorAll('.variants .js-variant-image').forEach((v) => v.classList.remove('active'));
    btn.classList.add('active');

    const imgUrl = btn.getAttribute('data-image') || btn.dataset.image;
    const color = btn.getAttribute('data-color') || btn.dataset.color || '';

    const mainImg = document.getElementById('main-product-image');
    if (mainImg && imgUrl) mainImg.src = imgUrl;

    const imageBadge = document.getElementById('main-image-badge');
    if (imageBadge) {
        imageBadge.textContent = color;
        imageBadge.style.display = color ? 'inline-block' : 'none';
    }

    const colorSpan = document.getElementById('selected-color');
    if (colorSpan) colorSpan.textContent = color;

    const colorLower = color.trim().toLowerCase();
    document.querySelectorAll('.thumbs .js-variant-image').forEach((thumb) => {
        const tc = (thumb.getAttribute('data-color') || thumb.dataset.color || '').trim().toLowerCase();
        if (colorLower && tc === colorLower) {
            thumb.classList.add('active');
        } else {
            thumb.classList.remove('active');
        }
    });
};

const purchaseForm = document.getElementById('purchase-form');
const variantDialog = document.getElementById('variant-dialog');
const variantSelect = document.getElementById('purchase-variant');
const variantInput = document.getElementById('selected-variant-id');
let pendingSubmitter = null;

if (purchaseForm && variantDialog && variantSelect) {
    purchaseForm.addEventListener('submit', (event) => {
        const selectedVal = variantInput ? variantInput.value : variantSelect.value;
        if (!selectedVal) {
            event.preventDefault();
            pendingSubmitter = event.submitter;
            variantDialog.showModal();
            document.body.style.overflow = 'hidden';
            variantSelect.focus();
        }
    });

    document.getElementById('variant-confirm')?.addEventListener('click', () => {
        if (!variantSelect.value) {
            document.getElementById('variant-feedback').textContent = 'Please select a color.';
            variantSelect.focus();
            return;
        }
        const matchingPill = document.querySelector(`.js-variant-item[data-variant-id="${variantSelect.value}"]`);
        if (matchingPill) {
            window.handleVariantClick(matchingPill);
        }
        variantDialog.close();
        document.body.style.overflow = '';
        purchaseForm.requestSubmit(pendingSubmitter);
    });

    document.getElementById('variant-close')?.addEventListener('click', () => variantDialog.close());
    variantDialog.addEventListener('close', () => document.body.style.overflow = '');
    variantDialog.addEventListener('click', (event) => { if (event.target === variantDialog) variantDialog.close(); });
    variantSelect.addEventListener('change', () => {
        const option = variantSelect.selectedOptions[0];
        document.getElementById('variant-feedback').textContent = option.value ? option.textContent : 'Select an available color to continue.';
        if (option.dataset.image) {
            const dlgImg = variantDialog.querySelector('.variant-dialog-image');
            if (dlgImg) dlgImg.src = option.dataset.image;
        }
    });
}

if (typeof window.fbq === 'function') {
    window.fbq('track', 'ViewContent', {
        content_name: @json($product->name),
        content_category: @json($product->category?->name ?? 'Handbags'),
        content_ids: [@json((string)($product->sku ?: $product->id))],
        content_type: 'product',
        value: {{ (float) ($defaultVariant ? $defaultVariant->effective_price : $product->effective_price) }},
        currency: 'BDT'
    }, {
        eventID: @json($metaEventId ?? '')
    });
}
</script>
@endpush

