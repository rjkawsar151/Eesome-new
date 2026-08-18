@extends('layouts.app')
@section('title', 'Shop Handbags')
@push('styles')
<style>
.shop-head{padding:3.5rem 0 2.25rem;background:linear-gradient(135deg,var(--brand-50),#fff)}.shop-head h1{font-size:clamp(2rem,5vw,3.5rem);margin:0 0 .5rem}.catalog{padding-top:2rem}.filters{display:grid;grid-template-columns:minmax(220px,1fr) minmax(190px,.45fr) auto;gap:.75rem;align-items:end;margin:0 0 2rem;padding:1rem;background:#f9fafb;border:1px solid #f0e4eb;border-radius:18px;box-shadow:0 4px 18px rgba(17,24,39,.04)}.filter-field{display:grid;gap:.35rem}.filter-field span{font-size:.75rem;font-weight:700;color:#6b7280}.filters input,.filters select,.filters button{height:46px;padding:0 1rem;border-radius:11px;font:inherit}.filters input,.filters select{width:100%;border:1px solid #d1d5db;background:#fff}.filters input:focus,.filters select:focus{border-color:var(--brand-400);outline:3px solid var(--brand-100)}.filters button{border:0}.filter-status{grid-column:1/-1;min-height:18px;font-size:.78rem;color:var(--text-muted)}
.product-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:1rem}.product-card{position:relative;border:1px solid #f0e4eb;border-radius:18px;overflow:hidden;background:#fff;transition:transform .25s,box-shadow .25s,border-color .25s}.product-card:hover{transform:translateY(-5px);border-color:var(--brand-100);box-shadow:0 16px 35px rgba(131,24,67,.12)}.product-media{display:block;overflow:hidden;background:#f9fafb;aspect-ratio:4/5}.product-img{width:100%;height:100%;object-fit:cover;display:block;transition:transform .4s}.product-card:hover .product-img{transform:scale(1.045)}.wishlist{position:absolute;z-index:3;top:.65rem;right:.65rem;margin:0}.wishlist form{margin:0}.wishlist button,.wishlist a{width:38px;height:38px;padding:0;border:1px solid rgba(255,255,255,.7);border-radius:50%;display:grid;place-items:center;background:rgba(255,255,255,.9);color:#6b7280;box-shadow:0 3px 12px rgba(0,0,0,.12);cursor:pointer;text-decoration:none;transition:all .2s}.wishlist button:hover,.wishlist a:hover,.wishlist .active{background:var(--brand-600);color:#fff}.product-body{padding:.95rem}.product-title{font-weight:700;color:var(--text-primary);text-decoration:none;line-height:1.4;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;min-height:2.8em}.price{color:var(--brand-700);font-weight:800}.old{text-decoration:line-through;color:#9ca3af;font-size:.8rem}.actions{display:grid;grid-template-columns:1fr;gap:.4rem;margin-top:.8rem}.actions form{width:100%;margin:0}.actions button{width:100%;min-height:44px;border:0;border-radius:9px;background:var(--brand-600);color:#fff;font-weight:700;cursor:pointer;white-space:nowrap}.details{display:block;width:max-content;margin:0 auto;padding:.3rem .5rem;color:var(--brand-700);font-size:.82rem;font-weight:700;text-align:center;text-decoration:none}.details:hover{text-decoration:underline}.infinite-status{min-height:70px;display:flex;align-items:center;justify-content:center;margin-top:1.25rem;color:var(--text-muted);font-size:.9rem}.infinite-status.loading::before{content:'';width:22px;height:22px;margin-right:.65rem;border:3px solid var(--brand-100);border-top-color:var(--brand-600);border-radius:50%;animation:spin .7s linear infinite}@keyframes spin{to{transform:rotate(360deg)}}
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
                    <div class="actions">
                        @if($product->stock > 0 || $product->available_for_preorder)<form method="POST" action="{{ route('cart.store') }}">@csrf<input type="hidden" name="product_id" value="{{ $product->id }}"><button>{{ ($product->stock <= 0 || $product->available_for_preorder) ? 'Pre-order' : 'Add to cart' }}</button></form>@endif
                        <a class="details" href="{{ route('products.show', $product->slug ?? $product->id) }}">Details</a>
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
