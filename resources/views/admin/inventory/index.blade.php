@extends('layouts.admin')
@section('title','Inventory')
@section('heading','Inventory')
@section('content')
<h1 class="title">Inventory</h1><p class="subtle">Adjust stock safely with a permanent movement history.</p>
<form class="toolbar" method="GET"><div class="field"><label>Search</label><input class="input" name="q" value="{{ request('q') }}" placeholder="Product or SKU"></div><button class="btn btn-soft">Filter</button></form>
<div class="card table-wrap"><table class="table"><thead><tr><th>Product</th><th>SKU</th><th>Stock</th><th>Adjustment</th></tr></thead><tbody>
@foreach($products as $product)<tr><td>{{ $product->name }}</td><td>{{ $product->sku ?: '—' }}</td><td><span class="badge {{ $product->stock <= 5 ? 'badge-red' : 'badge-green' }}">{{ $product->stock }}</span></td><td><form method="POST" action="{{ route('admin.inventory.adjust',$product) }}" class="inline-form">@csrf<input class="input" type="number" name="quantity_delta" required placeholder="+/-"><input class="input" name="reference" required placeholder="Reason/reference"><button class="btn btn-primary">Adjust</button></form></td></tr>@endforeach
</tbody></table></div><div class="pagination">{{ $products->links() }}</div>
<h2 style="margin-top:2rem">Recent stock history</h2><div class="card table-wrap"><table class="table"><thead><tr><th>Time</th><th>Product</th><th>Type</th><th>Change</th><th>Before → After</th><th>Reference</th></tr></thead><tbody>@forelse($movements as $movement)<tr><td>{{ $movement->created_at?->format('d M Y H:i') }}</td><td>{{ $movement->product?->name ?? 'Deleted product' }}</td><td>{{ $movement->type }}</td><td>{{ $movement->quantity_delta > 0 ? '+' : '' }}{{ $movement->quantity_delta }}</td><td>{{ $movement->stock_before }} → {{ $movement->stock_after }}</td><td>{{ $movement->reference ?: '—' }}</td></tr>@empty<tr><td colspan="6">No stock movements yet.</td></tr>@endforelse</tbody></table></div>
@endsection
