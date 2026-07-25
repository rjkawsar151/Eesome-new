@extends('layouts.app')

@section('title', 'Home')

@push('styles')
<style>
.luxury-bar { min-height: 34px; padding: .45rem 1rem; display: flex; align-items: center; justify-content: center; gap: 1.5rem; flex-wrap: wrap; background: #17121a; color: #fce7f3; font-size: .72rem; letter-spacing: .08em; text-transform: uppercase; }
/* ── Hero ── */
.hero { min-height: 82vh; padding: clamp(3rem, 7vw, 6rem) 0; background: radial-gradient(circle at 78% 30%, rgba(190,24,93,.12), transparent 28%), linear-gradient(135deg, #fff 0%, #fdf2f8 48%, #fce7f3 100%); display: flex; align-items: center; position: relative; overflow: hidden; }
.hero::before { content: ''; position: absolute; inset: 0; background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23db2777' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E"); }
.hero-inner { max-width: 1280px; margin: 0 auto; padding: 0 1.5rem; display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; align-items: center; width: 100%; position: relative; z-index: 1; }
.hero-badge { display: inline-block; background: linear-gradient(90deg, var(--brand-600), var(--brand-400)); color: #fff; font-size: .75rem; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; padding: .35rem 1rem; border-radius: 999px; margin-bottom: 1.25rem; }
.hero h1 { font-family: Georgia, 'Times New Roman', serif; font-size: clamp(2.6rem, 5.5vw, 5rem); font-weight: 500; line-height: 1.03; letter-spacing: -.035em; margin: 0 0 1.25rem; color: var(--text-primary); }
.hero h1 span { background: linear-gradient(135deg, var(--brand-700), var(--brand-400)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
.hero p { font-size: 1.1rem; color: var(--text-muted); line-height: 1.7; margin: 0 0 2rem; }
.hero-cta { display: flex; gap: 1rem; flex-wrap: wrap; }
.btn-primary { display: inline-block; padding: .85rem 2rem; background: linear-gradient(135deg, var(--brand-700), var(--brand-600)); color: #fff; border-radius: 999px; font-weight: 700; text-decoration: none; font-size: .95rem; transition: all .25s; box-shadow: 0 4px 20px rgba(219,39,119,0.3); }
.btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 28px rgba(219,39,119,0.4); }
.btn-outline { display: inline-block; padding: .85rem 2rem; border: 2px solid var(--brand-600); color: var(--brand-600); border-radius: 999px; font-weight: 700; text-decoration: none; font-size: .95rem; transition: all .25s; }
.btn-outline:hover { background: var(--brand-50); }
.hero-img-wrap { display: flex; justify-content: center; align-items: center; }
.hero-img-blob { width: 500px; max-width: 100%; aspect-ratio: 1; border-radius: 60% 40% 70% 30% / 50% 60% 40% 50%; background: linear-gradient(135deg, var(--brand-100), var(--brand-400) 70%); display: flex; align-items: center; justify-content: center; animation: blobMorph 8s ease-in-out infinite, luxuryFloat 5s ease-in-out infinite; box-shadow: 0 30px 80px rgba(131,24,67,.2); overflow: hidden; border: 8px solid rgba(255,255,255,.55); }
@keyframes blobMorph { 0%,100% { border-radius: 60% 40% 70% 30%/50% 60% 40% 50%; } 50% { border-radius: 40% 60% 30% 70%/60% 40% 60% 40%; } }
@keyframes luxuryFloat { 0%,100% { transform: translateY(0) rotate(-1deg); } 50% { transform: translateY(-12px) rotate(1deg); } }
.hero-img-blob img { width: 100%; height: 100%; object-fit: cover; }
.hero-stats { display: flex; gap: 2rem; margin-top: 2.5rem; flex-wrap: wrap; }
.stat { text-align: center; }
.stat strong { display: block; font-size: 1.5rem; font-weight: 800; color: var(--brand-700); }
.stat span { font-size: .78rem; color: var(--text-muted); font-weight: 500; }

/* ── Section headers ── */
.section-header { text-align: center; margin-bottom: 2.5rem; }
.section-header h2 { font-family: Georgia, 'Times New Roman', serif; font-size: clamp(2rem, 3.5vw, 3.2rem); font-weight: 500; letter-spacing: -.025em; color: var(--text-primary); margin: 0 0 .5rem; }
.section-header p { color: var(--text-muted); font-size: 1rem; }
.section-tag { display: inline-block; background: var(--brand-50); color: var(--brand-700); border: 1px solid var(--brand-100); font-size: .75rem; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; padding: .25rem .85rem; border-radius: 999px; margin-bottom: .75rem; }

/* ── Category Grid ── */
.cat-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 1.25rem; }
.cat-card { background: var(--surface-alt); border-radius: 20px; overflow: hidden; text-decoration: none; color: var(--text-primary); transition: transform .25s, box-shadow .25s; border: 1px solid var(--brand-100); text-align: center; padding: 1.5rem 1rem; }
.cat-card:hover { transform: translateY(-5px); box-shadow: 0 12px 30px rgba(219,39,119,0.1); }
.cat-card img { width: 80px; height: 80px; object-fit: cover; border-radius: 50%; margin: 0 auto .75rem; display: block; border: 3px solid var(--brand-100); }
.cat-card-name { font-weight: 600; font-size: .9rem; }

/* ── Featured Marquee ── */
.featured-section { background: linear-gradient(135deg, var(--brand-50), #fff0f9); padding: 3rem 0; }
.featured-marquee { overflow-x: auto; scrollbar-width: thin; padding: .5rem 1.5rem 1.25rem; }
.featured-marquee__track { display: flex; width: max-content; gap: 1.25rem; margin: 0 auto; }
.featured-marquee__group { display: contents; }
.featured-card { width: 200px; flex-shrink: 0; background: #fff; border-radius: 16px; overflow: hidden; border: 1px solid var(--brand-100); transition: transform .25s, box-shadow .25s; text-decoration: none; color: var(--text-primary); display: block; }
.featured-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(219,39,119,0.12); }
.featured-card img { width: 100%; height: 160px; object-fit: cover; }
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
.btn-cart { flex: 1; padding: .5rem; background: var(--brand-50); color: var(--brand-700); border: 1px solid var(--brand-100); border-radius: 10px; font-size: .78rem; font-weight: 600; cursor: pointer; transition: all .2s; text-align: center; }
.btn-cart:hover { background: var(--brand-600); color: #fff; border-color: var(--brand-600); }
.btn-buy { flex: 1; padding: .5rem; background: var(--brand-600); color: #fff; border: none; border-radius: 10px; font-size: .78rem; font-weight: 600; cursor: pointer; transition: background .2s; text-align: center; text-decoration: none; display: flex; align-items: center; justify-content: center; }
.btn-buy:hover { background: var(--brand-700); }
.btn-disabled { opacity: .5; cursor: not-allowed; pointer-events: none; }

/* ── Testimonials ── */
.testi-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1.5rem; max-width: 1100px; margin: 0 auto; }
.testi-card { background: #fff; border-radius: 20px; padding: 1.5rem; border: 1px solid var(--brand-100); box-shadow: 0 2px 12px rgba(219,39,119,0.05); }
.testi-stars { color: #fbbf24; font-size: 1rem; margin-bottom: .75rem; }
.testi-text { font-size: 1rem; color: #2d2d2d; line-height: 1.7; margin-bottom: 1rem; }
.testi-author { display: flex; align-items: center; gap: .75rem; }
.testi-avatar { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; background: var(--brand-100); display: flex; align-items: center; justify-content: center; font-weight: 700; color: var(--brand-700); flex-shrink: 0; }
.testi-name { font-weight: 600; font-size: .88rem; }

/* ── Pagination ── */
.pagination-wrap { margin-top: 2.5rem; display: flex; justify-content: center; }
.pagination-wrap nav { display: flex; gap: .5rem; }
.catalog-empty { grid-column: 1 / -1; padding: 3rem 1.5rem; text-align: center; border: 1px dashed var(--brand-400); border-radius: 20px; background: var(--brand-50); color: var(--text-muted); }
.catalog-empty strong { display: block; margin-bottom: .4rem; color: var(--text-primary); font-size: 1.1rem; }

@media (max-width: 768px) {
    .luxury-bar span:nth-child(n+3) { display: none; }
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

<div class="luxury-bar">
    <span>Complimentary delivery over ৳8,000</span><span>•</span>
    <span>Curated women’s handbags</span><span>•</span>
    <span>Easy 7-day exchange</span>
</div>

{{-- ── HERO ── --}}
<section class="hero">
    <div class="hero-inner">
        <div>
            <div class="hero-badge">The Signature Collection</div>
            <h1>The finishing touch to <span>every story</span></h1>
            <p>Discover refined women’s handbags selected for modern life—timeless silhouettes, thoughtful details, and effortless elegance.</p>
            <div class="hero-cta">
                <a href="{{ route('products.index') }}" class="btn-primary">Shop Now</a>
                <a href="#featured" class="btn-outline">View Featured</a>
            </div>
            <div class="hero-stats">
                <div class="stat"><strong>Curated</strong><span>Premium designs</span></div>
                <div class="stat"><strong>Secure</strong><span>Trusted checkout</span></div>
                <div class="stat"><strong>7 Days</strong><span>Easy exchange</span></div>
            </div>
        </div>
        <div class="hero-img-wrap">
            <div class="hero-img-blob">
                @php
                    $heroProduct = $featuredProducts->first();
                @endphp
                <img src="{{ app(\App\Services\ProductImageResolver::class)->resolve($heroProduct?->images?->first()?->image_path ?? $heroProduct?->image) }}"
                     onerror="this.onerror=null;this.src='{{ app(\App\Services\ProductImageResolver::class)->placeholder() }}'"
                     alt="EEsome premium women’s handbag">
            </div>
        </div>
    </div>
</section>

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
            <a href="{{ route('products.index') }}?category={{ $cat->id }}" class="cat-card">
                @if($cat->image)
                    <img src="{{ app(\App\Services\ProductImageResolver::class)->resolve($cat->image) }}"
                         alt="{{ $cat->name }}" loading="lazy">
                @else
                    <div style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,var(--brand-100),var(--brand-400));margin:0 auto .75rem;display:flex;align-items:center;justify-content:center;font-size:2rem;">👜</div>
                @endif
                <div class="cat-card-name">{{ $cat->name }}</div>
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
        <div class="product-grid">
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
                            @if($product->stock > 0 || $product->available_for_preorder)
                                <form method="POST" action="{{ route('cart.store') }}" style="flex:1">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="btn-cart" style="width:100%">Add to Cart</button>
                                </form>
                                <a href="{{ route('products.show', $product->slug ?? $product->id) }}" class="btn-buy">Buy Now</a>
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

        {{-- Pagination --}}
        <div class="pagination-wrap">
            {{ $allProducts->links() }}
        </div>
    </div>
</section>

@endsection
