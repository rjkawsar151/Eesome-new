@extends('layouts.app')
@section('title', 'Shop Handbags')
@push('styles')
<style>
.shop-head{padding:3.5rem 0 2.25rem;background:linear-gradient(135deg,var(--brand-50),#fff)}.shop-head h1{font-size:clamp(2rem,5vw,3.5rem);margin:0 0 .5rem}.catalog{padding-top:2rem}.filters{display:grid;grid-template-columns:minmax(220px,1fr) minmax(190px,.45fr) auto;gap:.75rem;align-items:end;margin:0 0 2rem;padding:1rem;background:#f9fafb;border:1px solid #f0e4eb;border-radius:18px;box-shadow:0 4px 18px rgba(17,24,39,.04)}.filter-field{display:grid;gap:.35rem}.filter-field span{font-size:.75rem;font-weight:700;color:#6b7280}.filters input,.filters select,.filters button{height:46px;padding:0 1rem;border-radius:11px;font:inherit}.filters input,.filters select{width:100%;border:1px solid #d1d5db;background:#fff}.filters input:focus,.filters select:focus{border-color:var(--brand-400);outline:3px solid var(--brand-100)}.filters button{border:0}.filter-status{grid-column:1/-1;min-height:18px;font-size:.78rem;color:var(--text-muted)}
.actions{display:flex;gap:.5rem;margin-top:.8rem;width:100%}.actions form{width:100%;margin:0}.actions .btn-cart{flex:1;min-height:42px;border:1px solid rgba(190,24,93,.18);border-radius:9px;background:#e1d0f0;color:#1e1b4b;font-weight:700;font-size:.78rem;cursor:pointer;transition:all .2s;text-align:center}.actions .btn-cart:hover{background:#d4bee8;color:#1e1b4b;border-color:rgba(190,24,93,.3)}.actions .btn-buy{flex:1;min-height:42px;border:1px solid rgba(126,34,206,.2);border-radius:9px;background:#7e22ce;color:#fff;font-weight:800;font-size:.78rem;cursor:pointer;transition:all .2s;text-align:center;text-decoration:none;display:flex;align-items:center;justify-content:center}.actions .btn-buy:hover{background:#6b21a8;color:#fff;border-color:rgba(107,33,168,.3)}.details{display:block;width:max-content;margin:.4rem auto 0;color:var(--brand-700);font-size:.82rem;font-weight:700;text-align:center;text-decoration:none}.details:hover{text-decoration:underline}.infinite-status{min-height:70px;display:flex;align-items:center;justify-content:center;margin-top:1.25rem;color:var(--text-muted);font-size:.9rem}.infinite-status.loading::before{content:'';width:22px;height:22px;margin-right:.65rem;border:3px solid var(--brand-100);border-top-color:var(--brand-600);border-radius:50%;animation:spin .7s linear infinite}@keyframes spin{to{transform:rotate(360deg)}}
@media(min-width:768px){.product-grid{grid-template-columns:repeat(4,minmax(0,1fr));gap:1.5rem}}
@media(max-width:640px){.filters{grid-template-columns:1fr}.filter-status{grid-column:auto}.catalog{padding-top:1rem}}
</style>
@endpush
@section('content')
<section class="shop-head"><div class="container"><h1>Find your next favourite</h1><p style="color:var(--text-muted)">Everyday essentials, statement pieces, and gifts worth remembering.</p></div></section>
<main class="container catalog">
    <form id="catalog-filters" class="filters" method="GET">
        <label class="filter-field"><span>Search products</span><input id="catalog-search" type="search" name="search" value="{{ request('search') }}" placeholder="Name or SKU" autocomplete="off"></label>
        <label class="filter-field"><span>Category</span><select id="catalog-category" name="category"><option value="">All categories</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected((string)request('category') === (string)$category->id)>{{ $category->name }}</option>@endforeach</select></label>
        <button class="nav-btn nav-btn-fill" type="submit">Apply filters</button>
        <div id="filter-status" class="filter-status" aria-live="polite">@if(request()->hasAny(['search','category']))Showing filtered results · <a href="{{ route('products.index') }}">Clear filters</a>@else Search updates automatically as you type.@endif</div>
    </form>
    @if($products->count())
        <div id="product-grid" class="product-grid">
        @foreach($products as $product)
            @php($image = $product->images->first()?->image_path ?? $product->image)
            @php($wishlisted = in_array($product->id, $wishlistIds, true))
            <article class="product-card">
                @if($badge = $product->badge_info)
                    <span style="position:absolute;top:0.65rem;left:0.65rem;z-index:3;padding:0.2rem 0.55rem;border-radius:999px;font-size:0.65rem;font-weight:800;letter-spacing:0.05em;background:{{ $badge['type']==='warning'?'#fef3c7':($badge['type']==='danger'?'#fee2e2':($badge['type']==='sale'?'#fce7f3':'#dbeafe')) }};color:{{ $badge['type']==='warning'?'#92400e':($badge['type']==='danger'?'#991b1b':($badge['type']==='sale'?'#9d174d':'#1e40af')) }}">{{ $badge['text'] }}</span>
                @endif
                <div class="wishlist">
                    @auth<form method="POST" action="{{ route('products.wishlist.toggle', $product) }}">@csrf<button class="{{ $wishlisted ? 'active' : '' }}" aria-label="{{ $wishlisted ? 'Remove from' : 'Add to' }} wishlist" title="{{ $wishlisted ? 'Remove from wishlist' : 'Add to wishlist' }}"><span aria-hidden="true">{{ $wishlisted ? '♥' : '♡' }}</span></button></form>
                    @else<a href="{{ route('login') }}" aria-label="Log in to add to wishlist" title="Log in to save">♡</a>@endauth
                </div>
                <a class="product-media" href="{{ route('products.show', $product->slug ?? $product->id) }}"><img class="product-img" src="{{ app(\App\Services\ProductImageResolver::class)->resolve($image) }}" onerror="this.onerror=null;this.src='{{ app(\App\Services\ProductImageResolver::class)->placeholder() }}'" alt="{{ $product->name }}" loading="lazy"></a>
                <div class="product-body">
                    <a class="product-title" href="{{ route('products.show', $product->slug ?? $product->id) }}">{{ $product->name }}</a>
                    <div><span class="price">৳{{ number_format((float)$product->effective_price, 0) }}</span> @if($product->has_discount)<span class="old">৳{{ number_format((float)$product->price, 0) }}</span>@endif</div>
                    @if($product->stock <= 0 || $product->available_for_preorder)
                        <div style="font-size:0.75rem;color:#92400e;font-weight:600;margin-top:0.25rem">Pre-order · 25–35 days</div>
                    @endif
                    @php
                        $hasMultipleVariants = $product->has_variants && $product->activeVariants->count() > 1;
                        $usesVariants = $product->has_variants && $product->activeVariants->isNotEmpty();
                        $canPurchase = $usesVariants
                            ? $product->activeVariants->contains(fn ($v) => $v->stock > 0)
                            : ($product->stock > 0 || $product->available_for_preorder);
                    @endphp
                    <div class="actions">
                        @if($canPurchase)
                            <form class="js-card-purchase" method="POST" action="{{ route('cart.store') }}" data-product-name="{{ $product->name }}" style="display:flex;flex:1;gap:.5rem;width:100%">
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
                            <button class="btn-cart btn-disabled" style="flex:1" disabled>Sold out</button>
                        @endif
                    </div>
                </div>
            </article>
        @endforeach
        </div>
        <div id="infinite-status" class="infinite-status" data-next-page="{{ $products->nextPageUrl() }}">Scroll for more products</div>
    @else
        <div style="text-align:center;padding:5rem 1rem"><h2>No products found</h2><p>Try a different search or category.</p></div>
    @endif
</main>
@endsection
@push('scripts')
<script>
(() => {
    const form = document.getElementById('catalog-filters');
    const search = document.getElementById('catalog-search');
    const category = document.getElementById('catalog-category');
    const status = document.getElementById('filter-status');
    let timer;
    const submit = () => {
        status.textContent = 'Updating products…';
        form.requestSubmit();
    };
    category.addEventListener('change', submit);
    search.addEventListener('input', () => {
        clearTimeout(timer);
        status.textContent = 'Waiting for you to finish typing…';
        timer = setTimeout(submit, 450);
    });
})();

(() => {
    const grid = document.getElementById('product-grid');
    const status = document.getElementById('infinite-status');
    if (!grid || !status) return;

    let loading = false;
    const observer = new IntersectionObserver(async (entries) => {
        if (!entries[0].isIntersecting || loading || !status.dataset.nextPage) return;

        loading = true;
        status.classList.add('loading');
        status.textContent = 'Loading 8 more products…';

        try {
            const response = await fetch(status.dataset.nextPage, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin'
            });
            if (!response.ok) throw new Error('Unable to load products');

            const documentFragment = new DOMParser().parseFromString(await response.text(), 'text/html');
            const newCards = documentFragment.querySelectorAll('#product-grid .product-card');
            const nextStatus = documentFragment.getElementById('infinite-status');

            newCards.forEach((card) => grid.appendChild(card));
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
