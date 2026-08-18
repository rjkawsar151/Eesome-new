@extends('layouts.app')

@section('title', 'Home')

@push('styles')
<style>
.luxury-bar { min-height: 34px; overflow: hidden; padding: .45rem 0; background: #17121a; color: #fce7f3; font-size: .72rem; letter-spacing: .08em; text-transform: uppercase; }
.luxury-bar__track { display: flex; width: max-content; animation: luxuryBarMarquee 20s linear infinite; will-change: transform; }
.luxury-bar__group { display: flex; flex-shrink: 0; align-items: center; gap: 1.5rem; padding-right: 1.5rem; white-space: nowrap; }
.luxury-bar__group span::after { content: '•'; margin-left: 1.5rem; color: var(--brand-400); }
.luxury-bar:hover .luxury-bar__track { animation-play-state: paused; }
@keyframes luxuryBarMarquee { to { transform: translateX(-50%); } }
@media (prefers-reduced-motion: reduce) {
    .luxury-bar { overflow-x: auto; }
    .luxury-bar__track { animation: none; }
    .luxury-bar__group[aria-hidden="true"] { display: none; }
}
/* ── Section headers ── */
.section-header { text-align: center; margin-bottom: 2.5rem; }
.section-header h2 { font-family: Georgia, 'Times New Roman', serif; font-size: clamp(2rem, 3.5vw, 3.2rem); font-weight: 500; letter-spacing: -.025em; color: var(--text-primary); margin: 0 0 .5rem; }
.section-header p { color: var(--text-muted); font-size: 1rem; }
.section-tag { display: inline-block; background: var(--brand-50); color: var(--brand-700); border: 1px solid var(--brand-100); font-size: .75rem; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; padding: .25rem .85rem; border-radius: 999px; margin-bottom: .75rem; }

/* ── Category Grid ── */
.cat-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: clamp(.75rem, 2vw, 1.5rem); }
.cat-card { position: relative; display: block; aspect-ratio: 4 / 5; background: var(--surface-alt); border-radius: 20px; overflow: hidden; text-decoration: none; transition: transform .25s, box-shadow .25s; border: 1px solid var(--brand-100); }
.cat-card:hover { transform: translateY(-5px); box-shadow: 0 16px 36px rgba(219,39,119,0.16); }
.cat-card:focus-visible { outline: 3px solid var(--brand-500); outline-offset: 3px; }
.cat-card img { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform .4s ease; }
.cat-card:hover img { transform: scale(1.035); }
.cat-card::after { content: ''; position: absolute; inset: 42% 0 0; background: linear-gradient(to bottom, transparent, rgba(23,18,26,.88)); pointer-events: none; }
.cat-card__content { position: absolute; z-index: 1; left: 1rem; right: 1rem; bottom: 1rem; display: flex; align-items: flex-end; justify-content: space-between; gap: .75rem; color: #fff; }
.cat-card__name { margin: 0; font-family: Georgia, 'Times New Roman', serif; font-size: clamp(1.15rem, 2vw, 1.55rem); font-weight: 600; line-height: 1.15; text-shadow: 0 1px 8px rgba(0,0,0,.3); }
.cat-card__explore { flex-shrink: 0; display: inline-flex; align-items: center; justify-content: center; min-height: 36px; padding: .55rem .85rem; border-radius: 999px; background: #fff; color: var(--brand-700); font-size: .75rem; font-weight: 800; letter-spacing: .04em; text-transform: uppercase; box-shadow: 0 4px 14px rgba(0,0,0,.16); transition: transform .2s, background .2s, color .2s; }
.cat-card:hover .cat-card__explore { transform: translateX(3px); background: var(--brand-600); color: #fff; }
@media (min-width: 640px) { .cat-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); } }
@media (min-width: 1024px) { .cat-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); } }

/* ── Featured Marquee ── */
.featured-section { background: linear-gradient(135deg, var(--brand-50), #fff0f9); padding: 3rem 0; }
.featured-marquee { overflow: hidden; padding: .5rem 0 1.25rem; }
.featured-marquee__track { display: flex; width: max-content; gap: 1.25rem; animation: featuredMarquee 28s linear infinite; will-change: transform; }
.featured-marquee__group { display: flex; flex-shrink: 0; gap: 1.25rem; }
.featured-marquee:hover .featured-marquee__track { animation-play-state: paused; }
@keyframes featuredMarquee { to { transform: translateX(calc(-50% - .625rem)); } }
@media (prefers-reduced-motion: reduce) {
    .featured-marquee { overflow-x: auto; }
    .featured-marquee__track { animation: none; }
    .featured-marquee__group[aria-hidden="true"] { display: none; }
}
.featured-card { width: 200px; flex-shrink: 0; background: #fff; border-radius: 16px; overflow: hidden; border: 1px solid var(--brand-100); transition: transform .25s, box-shadow .25s; text-decoration: none; color: var(--text-primary); display: block; }
.featured-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(219,39,119,0.12); }
.featured-card img { width: 100%; height: 160px; object-fit: contain; object-position: center; display: block; padding: .4rem; background: var(--surface-alt); }
.featured-card-body { padding: .75rem; }
.featured-card-name { font-size: .82rem; font-weight: 600; margin-bottom: .25rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.featured-card-price { font-size: .85rem; color: var(--brand-700); font-weight: 700; }

/* ── Product Grid ── */
.product-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; }
@media (min-width: 768px) { .product-grid { grid-template-columns: repeat(4, 1fr); gap: 1.5rem; } }
.product-card { background: #fff; border-radius: 20px; overflow: hidden; border: 1px solid #f0e4eb; transition: box-shadow .25s, transform .25s; position: relative; display: block; text-decoration: none; color: var(--text-primary); }
.product-card:hover { transform: translateY(-4px); box-shadow: 0 12px 30px rgba(219,39,119,0.12); }
.product-card:focus-within { outline: 3px solid var(--brand-400); outline-offset: 2px; }
.product-card__img-wrap { position: relative; aspect-ratio: 3/4; overflow: hidden; background: var(--surface-alt); }
.product-card__img { width: 100%; height: 100%; object-fit: cover; transition: transform .4s; display: block; }
.product-card:hover .product-card__img { transform: scale(1.05); }
.product-card__badge { position: absolute; top: .6rem; left: .6rem; font-size: .65rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; padding: .25rem .65rem; border-radius: 999px; z-index: 2; }
.badge-danger { background: #ef4444; color: #fff; }
.badge-warning { background: #f59e0b; color: #fff; }
.badge-sale { background: var(--brand-600); color: #fff; }
.badge-info { background: #3b82f6; color: #fff; }
.badge-custom { background: var(--brand-900); color: #fff; }
.product-card__body { padding: .85rem; }
.product-card__name { font-size: .88rem; font-weight: 600; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; margin-bottom: .4rem; }
.product-card__price { display: flex; align-items: center; gap: .5rem; margin-bottom: .75rem; }
.preorder-note { margin: -.35rem 0 .7rem; color: #92400e; font-size: .72rem; font-weight: 600; }
.price-current { font-size: .95rem; font-weight: 700; color: var(--brand-700); }
.price-original { font-size: .8rem; text-decoration: line-through; color: #9ca3af; }
.product-card__actions { display: flex; gap: .5rem; }
.btn-cart { flex: 1; padding: .5rem; background: #e1d0f0; color: #1e1b4b; border: 1px solid rgba(190,24,93,.18); border-radius: 10px; font-size: .78rem; font-weight: 700; cursor: pointer; transition: all .2s; text-align: center; }
.btn-cart:hover { background: #d4bee8; color: #1e1b4b; border-color: rgba(190,24,93,.3); }
.btn-buy { flex: 1; padding: .5rem; background: #7e22ce; color: #fff; border: 1px solid rgba(126,34,206,.2); border-radius: 10px; font-size: .78rem; font-weight: 700; cursor: pointer; transition: all .2s; text-align: center; text-decoration: none; display: flex; align-items: center; justify-content: center; }
.btn-buy:hover { background: #6b21a8; color: #fff; border-color: rgba(107,33,168,.3); }
.btn-disabled { opacity: .5; cursor: not-allowed; pointer-events: none; }

/* ── Testimonials ── */
.testi-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1.5rem; max-width: 1100px; margin: 0 auto; }
.testi-card { background: #fff; border-radius: 20px; padding: 1.5rem; border: 1px solid var(--brand-100); box-shadow: 0 2px 12px rgba(219,39,119,0.05); }
.testi-stars { color: #fbbf24; font-size: 1rem; margin-bottom: .75rem; }
.testi-text { font-size: 1rem; color: #2d2d2d; line-height: 1.7; margin-bottom: 1rem; }
.testi-author { display: flex; align-items: center; gap: .75rem; }
.testi-avatar { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; background: var(--brand-100); display: flex; align-items: center; justify-content: center; font-weight: 700; color: var(--brand-700); flex-shrink: 0; }
.testi-name { font-weight: 600; font-size: .88rem; }

.infinite-status { min-height: 70px; display: flex; align-items: center; justify-content: center; margin-top: 1.25rem; color: var(--text-muted); font-size: .9rem; }
.infinite-status.loading::before { content: ''; width: 22px; height: 22px; margin-right: .65rem; border: 3px solid var(--brand-100); border-top-color: var(--brand-600); border-radius: 50%; animation: productSpin .7s linear infinite; }
@keyframes productSpin { to { transform: rotate(360deg); } }
.whatsapp-cta { margin: 1.5rem auto 0; max-width: 760px; padding: clamp(2rem, 5vw, 3.5rem); border-radius: 24px; text-align: center; background: linear-gradient(135deg, #f0fdf4, #ffffff); border: 1px solid #bbf7d0; }
.whatsapp-cta h2 { margin: 0 0 .65rem; font-size: clamp(1.55rem, 3vw, 2.25rem); }
.whatsapp-cta p { max-width: 520px; margin: 0 auto 1.4rem; color: var(--text-muted); line-height: 1.65; }
.whatsapp-cta__button { display: inline-flex; align-items: center; gap: .55rem; padding: .85rem 1.35rem; border-radius: 999px; background: #25d366; color: #fff; text-decoration: none; font-weight: 800; box-shadow: 0 0 0 0 rgba(37,211,102,.55); animation: whatsappGlow 2s infinite; }
@keyframes whatsappGlow { 70% { box-shadow: 0 0 0 14px rgba(37,211,102,0); } 100% { box-shadow: 0 0 0 0 rgba(37,211,102,0); } }

/* ── Pagination ── */
.pagination-wrap { margin-top: 2.5rem; display: flex; justify-content: center; }
.pagination-wrap nav { display: flex; gap: .5rem; }
.catalog-empty { grid-column: 1 / -1; padding: 3rem 1.5rem; text-align: center; border: 1px dashed var(--brand-400); border-radius: 20px; background: var(--brand-50); color: var(--text-muted); }
.catalog-empty strong { display: block; margin-bottom: .4rem; color: var(--text-primary); font-size: 1.1rem; }

@media (max-width: 768px) {
    .cat-card__content { flex-direction: column; align-items: flex-start; }
    .hero-inner { grid-template-columns: 1fr; text-align: center; }
    .hero-img-wrap { order: -1; }
    .hero-img-blob { width: 280px; }
    .hero-cta { justify-content: center; }
    .hero-stats { justify-content: center; }
    .testi-grid { grid-template-columns: 1fr; }
}
</style>
@endpush

@section('content')

<div class="luxury-bar" role="region" aria-label="Store announcements">
    <div class="luxury-bar__track">
        <div class="luxury-bar__group">
            <span>Complimentary delivery over 8,000</span>
            <span>Curated women's handbags</span>
            <span>Easy 7-day exchange</span>
        </div>
        <div class="luxury-bar__group" aria-hidden="true">
            <span>Complimentary delivery over 8,000</span>
            <span>Curated women's handbags</span>
            <span>Easy 7-day exchange</span>
        </div>
    </div>
</div>

{{-- HERO ── --}}
<x-product-showcase-carousel :products="$featuredProducts" />
{{-- ── CATEGORIES ── --}}
@if($categories->count())
<section class="section-gap">
    <div class="container">
        <div class="section-header">
            <span class="section-tag">Browse by Category</span>
            <h2>Shop by Collection</h2>
            <p>Find the perfect bag for every style and occasion</p>
        </div>
        <div class="cat-grid">
            @foreach($categories as $cat)
            @php
                $categoryPng = 'images/categories/' . $cat->slug . '.png';
                $categoryImage = is_file(public_path($categoryPng))
                    ? asset($categoryPng)
                    : app(\App\Services\ProductImageResolver::class)->resolve($cat->image);
            @endphp
            <a href="{{ route('products.index', ['category' => $cat->id]) }}"
               class="cat-card"
               aria-label="Shop {{ $cat->name }}">
                <img src="{{ $categoryImage }}"
                     onerror="this.onerror=null;this.src='{{ app(\App\Services\ProductImageResolver::class)->placeholder() }}'"
                     alt="{{ $cat->name }} collection" loading="lazy">
                <div class="cat-card__content"><h3 class="cat-card__name">{{ $cat->name }}</h3><span class="cat-card__explore">Explore &rarr;</span></div>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ── FEATURED MARQUEE ── --}}
@if($featuredProducts->count())
<section class="featured-section" id="featured">
    <div class="container">
        <div class="section-header">
            <span class="section-tag">Featured</span>
            <h2>Trending Right Now</h2>
            <p>Our most-loved handbags, curated just for you</p>
        </div>
    </div>
    <div class="featured-marquee" role="region" aria-label="Featured products marquee">
        <div class="featured-marquee__track">
            <div class="featured-marquee__group">
                @foreach($featuredProducts as $fp)
                <a href="{{ route('products.show', $fp->slug ?? $fp->id) }}" class="featured-card">
                    @php $img = $fp->images->first(); @endphp
                    <img src="{{ app(\App\Services\ProductImageResolver::class)->resolve($img?->image_path ?? $fp->image) }}"
                         onerror="this.onerror=null;this.src='{{ app(\App\Services\ProductImageResolver::class)->placeholder() }}'"
                         alt="{{ $fp->name }}" loading="lazy">
                    <div class="featured-card-body">
                        <div class="featured-card-name">{{ $fp->name }}</div>
                        <div class="featured-card-price">৳{{ number_format($fp->effective_price, 0) }}</div>
                    </div>
                </a>
                @endforeach
            </div>
            <div class="featured-marquee__group" aria-hidden="true">
                @foreach($featuredProducts as $fp)
                <a href="{{ route('products.show', $fp->slug ?? $fp->id) }}" class="featured-card">
                    @php $img = $fp->images->first(); @endphp
                    <img src="{{ app(\App\Services\ProductImageResolver::class)->resolve($img?->image_path ?? $fp->image) }}"
                         onerror="this.onerror=null;this.src='{{ app(\App\Services\ProductImageResolver::class)->placeholder() }}'"
                         alt="{{ $fp->name }}" loading="lazy" tabindex="-1">
                    <div class="featured-card-body">
                        <div class="featured-card-name">{{ $fp->name }}</div>
                        <div class="featured-card-price">৳{{ number_format($fp->effective_price, 0) }}</div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

{{-- ── ALL PRODUCTS GRID ── --}}
<section class="section-gap">
    <div class="container">
        <div class="section-header">
            <span class="section-tag">All Products</span>
            <h2>Our Collection</h2>
            <p>Explore our full range of premium handbags</p>
        </div>
        <div id="product-grid" class="product-grid">
            @foreach($allProducts as $product)
                @php $badge = $product->badge_info; @endphp
                <div class="product-card">
                    <a href="{{ route('products.show', $product->slug ?? $product->id) }}" style="text-decoration:none;color:inherit;display:block;">
                        <div class="product-card__img-wrap">
                            @php $img = $product->images->first(); @endphp
                            <img class="product-card__img"
                                 src="{{ app(\App\Services\ProductImageResolver::class)->resolve($img?->image_path ?? $product->image) }}"
                                 onerror="this.onerror=null;this.src='{{ app(\App\Services\ProductImageResolver::class)->placeholder() }}'"
                                 alt="{{ $product->name }}" loading="lazy">
                            @if($badge)
                                <span class="product-card__badge badge-{{ $badge['type'] }}">{{ $badge['text'] }}</span>
                            @endif
                        </div>
                        <div class="product-card__body">
                            <div class="product-card__name">{{ $product->name }}</div>
                            <div class="product-card__price">
                                <span class="price-current">৳{{ number_format($product->effective_price, 0) }}</span>
                                @if($product->has_discount)
                                    <span class="price-original">৳{{ number_format($product->price, 0) }}</span>
                                @endif
                            </div>
                            @if($product->available_for_preorder)<div class="preorder-note">Pre-order · dispatches in 25–35 days</div>@endif
                        </div>
                    </a>
                    <div style="padding:0 .85rem .85rem">
                        <div class="product-card__actions">
                            @php
                                $hasMultipleVariants = $product->has_variants && $product->activeVariants->count() > 1;
                                $usesVariants = $product->has_variants && $product->activeVariants->isNotEmpty();
                                $canPurchase = $usesVariants
                                    ? $product->activeVariants->contains(fn ($variant) => $variant->stock > 0)
                                    : ($product->stock > 0 || $product->available_for_preorder);
                            @endphp
                            @if($canPurchase)
                                <form class="js-card-purchase" method="POST" action="{{ route('cart.store') }}" data-product-name="{{ $product->name }}" style="display:flex;flex:1;gap:.5rem">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <input type="hidden" name="quantity" value="1">
                                    @if($hasMultipleVariants)
                                        <select class="js-card-variant" name="variant_id" hidden aria-label="Selected color">
                                            <option value="">Choose a color</option>
                                            @foreach($product->activeVariants as $variant)
                                                <option value="{{ $variant->id }}" @disabled($variant->stock < 1)>{{ $variant->color_name }} / SKU {{ $variant->sku }} / &#2547;{{ number_format((float)$variant->effective_price, 0) }}</option>
                                            @endforeach
                                        </select>
                                    @elseif($usesVariants)
                                        <input type="hidden" name="variant_id" value="{{ $product->activeVariants->first()->id }}">
                                    @endif
                                    <button type="submit" class="btn-cart" style="flex:1">Cart</button>
                                    <button type="submit" name="buy_now" value="1" class="btn-buy" style="flex:1">{{ ($product->stock <= 0 || $product->available_for_preorder) ? 'Pre-order' : 'Buy' }}</button>
                                </form>
                            @else
                                <button class="btn-cart btn-disabled" style="flex:1">Sold Out</button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
            @if($allProducts->isEmpty())
                <div class="catalog-empty">
                    <strong>No products are available yet.</strong>
                    The catalog will appear here as soon as products are added.
                </div>
            @endif
        </div>

        <div id="infinite-status" class="infinite-status" data-next-page="{{ $allProducts->nextPageUrl() }}" aria-live="polite">
            @if($allProducts->hasMorePages()) Scroll for more products @else You have reached the end of the collection. @endif
        </div>
</div>
</section>


<dialog id="landing-variant-dialog" class="landing-variant-dialog" aria-labelledby="landing-variant-title">
    <button type="button" class="landing-variant-close" aria-label="Close color selector">&times;</button>
    <p class="section-tag">Choose your color</p>
    <h2 id="landing-variant-title">Select a color</h2>
    <p id="landing-variant-product" class="landing-variant-product"></p>
    <label for="landing-variant-select">Available colors</label>
    <select id="landing-variant-select" class="landing-variant-select"></select>
    <p id="landing-variant-feedback" class="landing-variant-feedback">Select an available color to continue.</p>
    <button id="landing-variant-confirm" class="landing-variant-confirm" type="button">Continue</button>
</dialog>

@push('styles')
<style>
@keyframes landing-variant-in{from{opacity:0;transform:translateY(16px) scale(.975)}to{opacity:1;transform:translateY(0) scale(1)}}
@keyframes landing-backdrop-in{from{background:rgba(23,18,26,0)}to{background:rgba(23,18,26,.55)}}
.landing-variant-dialog{width:min(92vw,480px);padding:1.6rem;border:0;border-radius:22px;background:#fff;box-shadow:0 24px 70px rgba(25,15,22,.3)}
.landing-variant-dialog[open]{animation:landing-variant-in 280ms cubic-bezier(.22,1,.36,1) both}
.landing-variant-dialog[open]::backdrop{animation:landing-backdrop-in 240ms ease-out both}
.landing-variant-dialog::backdrop{background:rgba(23,18,26,.55);backdrop-filter:blur(3px)}
.landing-variant-close{position:absolute;right:.8rem;top:.8rem;width:40px;height:40px;border:0;border-radius:50%;background:var(--brand-50);font-size:1.35rem;cursor:pointer}
.landing-variant-dialog h2{margin:.3rem 0}.landing-variant-product{color:var(--text-muted)}
.landing-variant-select{width:100%;min-height:48px;margin:.5rem 0;padding:.75rem;border:1px solid var(--brand-100);border-radius:11px;background:#fff}
.landing-variant-feedback{min-height:1.25rem;color:var(--text-muted);font-size:.85rem}
.landing-variant-confirm{width:100%;min-height:48px;border:0;border-radius:11px;background:var(--brand-600);color:#fff;font-weight:800;cursor:pointer}
@media(max-width:600px){@keyframes landing-variant-in{from{opacity:0;transform:translateY(42px)}to{opacity:1;transform:translateY(0)}}.landing-variant-dialog{width:100%;max-width:none;margin:auto 0 0;border-radius:22px 22px 0 0}}
@media(prefers-reduced-motion:reduce){.landing-variant-dialog[open],.landing-variant-dialog[open]::backdrop{animation:none}}
</style>
@endpush

@push('scripts')
<script>
(() => {
    const dialog = document.getElementById('landing-variant-dialog');
    const modalSelect = document.getElementById('landing-variant-select');
    const productLabel = document.getElementById('landing-variant-product');
    const feedback = document.getElementById('landing-variant-feedback');
    let pendingForm = null;
    let pendingButton = null;
    let sourceSelect = null;

    document.addEventListener('submit', (event) => {
        const form = event.target.closest('.js-card-purchase');
        if (!form) return;
        const variant = form.querySelector('.js-card-variant');
        if (!variant || variant.value) return;

        event.preventDefault();
        pendingForm = form;
        pendingButton = event.submitter;
        sourceSelect = variant;
        modalSelect.replaceChildren(...[...variant.options].map((option) => option.cloneNode(true)));
        modalSelect.value = '';
        productLabel.textContent = form.dataset.productName || '';
        feedback.textContent = 'Select an available color to continue.';
        dialog.showModal();
        document.body.style.overflow = 'hidden';
        modalSelect.focus();
    });

    document.getElementById('landing-variant-confirm').addEventListener('click', () => {
        if (!modalSelect.value) {
            feedback.textContent = 'Please select a color.';
            modalSelect.focus();
            return;
        }
        sourceSelect.value = modalSelect.value;
        dialog.close();
        pendingForm.requestSubmit(pendingButton);
    });

    dialog.querySelector('.landing-variant-close').addEventListener('click', () => dialog.close());
    dialog.addEventListener('click', (event) => { if (event.target === dialog) dialog.close(); });
    dialog.addEventListener('close', () => {
        document.body.style.overflow = '';
        pendingButton?.focus();
    });
})();
</script>

<script>
(() => {
    const grid = document.getElementById('product-grid');
    const status = document.getElementById('infinite-status');
    if (!grid || !status || !status.dataset.nextPage) return;

    let loading = false;
    const observer = new IntersectionObserver(async (entries) => {
        if (!entries[0].isIntersecting || loading || !status.dataset.nextPage) return;
        loading = true;
        status.classList.add('loading');
        status.textContent = 'Loading 12 more products…';
        try {
            const response = await fetch(status.dataset.nextPage, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' });
            if (!response.ok) throw new Error('Unable to load products');
            const nextPage = new DOMParser().parseFromString(await response.text(), 'text/html');
            nextPage.querySelectorAll('#product-grid .product-card').forEach((card) => grid.appendChild(card));
            const nextStatus = nextPage.getElementById('infinite-status');
            status.dataset.nextPage = nextStatus?.dataset.nextPage || '';
            status.textContent = status.dataset.nextPage ? 'Scroll for more products' : 'You have reached the end of the collection.';
        } catch (error) {
            status.textContent = 'Products could not be loaded. Scroll away and back to retry.';
        } finally {
            status.classList.remove('loading');
            loading = false;
        }
    }, { rootMargin: '500px 0px' });
    observer.observe(status);
})();
</script>
@endpush

@endsection