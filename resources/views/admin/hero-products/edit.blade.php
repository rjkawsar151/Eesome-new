@extends('layouts.admin')
@section('title', 'Hero Products')
@section('heading', 'Hero Products')
@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;gap:1rem;flex-wrap:wrap">
    <div><h1 class="title">Homepage hero products</h1><p class="subtle">Choose which active products appear in the homepage hero and set their display order.</p></div>
    <a class="btn btn-soft" href="{{ route('home') }}" target="_blank" rel="noopener">Preview homepage</a>
</div>
<form method="POST" action="{{ route('admin.hero-products.update') }}" style="margin-top:1rem">
    @csrf
    @method('PUT')
    <div class="card table-wrap"><table class="table">
        <thead><tr><th>Hero</th><th>Product</th><th>Category</th><th>Price</th><th>Stock</th><th>Display order</th><th></th></tr></thead>
        <tbody>
        @forelse($products as $product)
            <tr>
                <td><input type="checkbox" name="featured[]" value="{{ $product->id }}" @checked($product->is_featured) aria-label="Show {{ $product->name }} in homepage hero"></td>
                <td><strong>{{ $product->name }}</strong><div class="subtle">{{ $product->sku }}</div></td>
                <td>{{ $product->category?->name ?? 'Uncategorized' }}</td>
                <td>&#2547;{{ number_format((float) $product->effective_price, 0) }}</td>
                <td>{{ $product->stock }}</td>
                <td><input class="input" style="width:100px" type="number" min="0" name="sort_order[{{ $product->id }}]" value="{{ old('sort_order.'.$product->id, $product->sort_order) }}"></td>
                <td><a class="btn btn-soft" href="{{ route('admin.products.edit', $product) }}">Edit product</a></td>
            </tr>
        @empty
            <tr><td colspan="7">No active products are available.</td></tr>
        @endforelse
        </tbody>
    </table></div>
    @if($products->isNotEmpty())
        <div style="margin-top:1rem"><button class="btn btn-primary">Save hero products</button></div>
    @endif
</form>
@endsection