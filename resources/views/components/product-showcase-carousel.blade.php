@props(['products'])

@if($products->isNotEmpty())
@php
    $imageResolver = app(\App\Services\ProductImageResolver::class);
@endphp
<section
    class="product-showcase"
    data-product-showcase
    data-count="{{ $products->count() }}"
    aria-roledescription="carousel"
    aria-label="Featured handbags"
    tabindex="0"
>
    <div class="product-showcase__inner">
        <div class="product-showcase__eyebrow">The Signature Collection</div>

        <div class="product-showcase__stage" data-carousel-stage>
            @foreach($products as $product)
                @php
                    $image = $product->images->first()?->image_path ?? $product->image;
                    $position = $loop->first ? 'active' : ($loop->index === 1 ? 'next' : ($loop->last && $products->count() > 2 ? 'previous' : 'hidden'));
                @endphp
                <button
                    type="button"
                    class="product-showcase__slide"
                    data-carousel-slide
                    data-index="{{ $loop->index }}"
                    data-position="{{ $position }}"
                    aria-label="Show {{ $product->name }}"
                    aria-current="{{ $loop->first ? 'true' : 'false' }}"
                    aria-hidden="{{ $position === 'hidden' ? 'true' : 'false' }}"
                    tabindex="-1"
                >
                    <span class="product-showcase__shadow" aria-hidden="true"></span>
                    <img
                        src="{{ $imageResolver->resolve($image) }}"
                        onerror="this.onerror=null;this.src='{{ $imageResolver->placeholder() }}'"
                        alt="{{ $product->name }}"
                        width="720"
                        height="720"
                        class="product-showcase__image"
                        loading="{{ $loop->first ? 'eager' : 'lazy' }}"
                        @if($loop->first) fetchpriority="high" @endif
                    >
                </button>
            @endforeach
        </div>

        <div class="product-showcase__content" aria-live="polite" aria-atomic="true">
            @foreach($products as $product)
                <div class="product-showcase__details" data-carousel-details data-index="{{ $loop->index }}" data-active="{{ $loop->first ? 'true' : 'false' }}">
                    @if($product->category?->name)
                        <p class="product-showcase__category">{{ $product->category->name }}</p>
                    @endif
                    <h1 class="product-showcase__title">{{ $product->name }}</h1>
                    @if(filled($product->effective_price))
                        <p class="product-showcase__price">৳{{ number_format((float) $product->effective_price, 0) }}</p>
                    @endif
                    <a class="product-showcase__cta" href="{{ route('products.show', $product->slug ?? $product->id) }}">View Bag</a>
                </div>
            @endforeach
        </div>

        @if($products->count() > 1)
            <div class="product-showcase__controls">
                <button type="button" class="product-showcase__control" data-carousel-previous aria-label="Show previous bag">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
                </button>
                <p class="product-showcase__status" aria-hidden="true"><span data-carousel-current>1</span> / {{ $products->count() }}</p>
                <button type="button" class="product-showcase__control" data-carousel-next aria-label="Show next bag">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
                </button>
            </div>
        @endif
    </div>
</section>
@endif