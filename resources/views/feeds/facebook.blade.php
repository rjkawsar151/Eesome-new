{!! '<' . '?xml version="1.0" encoding="UTF-8"?' . '>' !!}
<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">
    <channel>
        <title>{{ config('app.name', 'EEsome') }} Product Catalog</title>
        <link>{{ url('/') }}</link>
        <description>Meta Product Feed for {{ config('app.name', 'EEsome') }}</description>

        @inject('imageResolver', 'App\Services\ProductImageResolver')

        @foreach($products as $product)
            @php
                $primaryImage = $product->images->first()?->image_path ?? $product->image;
                $imageUrl = $imageResolver->resolve($primaryImage);
                $isAvailable = ($product->stock > 0 || $product->available_for_preorder) && ! $product->is_sold_out;
            @endphp
            <item>
                <!-- Required Meta Catalog Fields -->
                <g:id>{{ $product->sku ?: $product->id }}</g:id>
                <g:title><![CDATA[{{ $product->name }}]]></g:title>
                <g:description><![CDATA[{{ strip_tags($product->clean_description) }}]]></g:description>
                <g:link>{{ route('products.show', $product->slug) }}</g:link>
                <g:image_link>{{ $imageUrl }}</g:image_link>
                <g:availability>{{ $isAvailable ? 'in stock' : 'out of stock' }}</g:availability>
                <g:condition>new</g:condition>

                <!-- Pricing (BDT) -->
                @if($product->has_discount)
                    <g:price>{{ number_format((float) $product->price, 2, '.', '') }} BDT</g:price>
                    <g:sale_price>{{ number_format((float) $product->discount_price, 2, '.', '') }} BDT</g:sale_price>
                @else
                    <g:price>{{ number_format((float) $product->price, 2, '.', '') }} BDT</g:price>
                @endif

                <!-- Category & Brand Metadata -->
                @if($product->brand)
                    <g:brand><![CDATA[{{ $product->brand->name }}]]></g:brand>
                @else
                    <g:brand><![CDATA[{{ config('app.name', 'EEsome') }}]]></g:brand>
                @endif

                @if($product->category)
                    <g:product_type><![CDATA[{{ $product->category->name }}]]></g:product_type>
                @endif

                <!-- Additional Gallery Images -->
                @foreach($product->images->slice(1, 10) as $extraImage)
                    <g:additional_image_link>{{ $imageResolver->resolve($extraImage->image_path) }}</g:additional_image_link>
                @endforeach
            </item>
        @endforeach
    </channel>
</rss>
