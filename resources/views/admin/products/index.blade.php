@extends('layouts.admin') @section('title','Products') @section('heading','Products') @section('content')
@push('styles')
<style>
    .prod-cell{white-space:nowrap;min-width:0}
    .prod-thumb{display:flex;align-items:center;gap:.7rem;min-width:0}
    .prod-thumb img{width:46px;height:46px;object-fit:contain;border:1px solid var(--line);border-radius:.55rem;background:#fff;flex-shrink:0}
    .prod-meta{min-width:0;display:grid;gap:.05rem}
    .prod-name{font-weight:700;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
    .prod-sub{color:var(--muted);font-size:.78rem}
    .prod-count{font-size:.78rem;color:var(--brand)}
    @media(max-width:640px){
        .table-products tr{display:block;padding:.65rem .8rem .7rem;margin-bottom:.7rem}
        .table-products td{display:block;width:100%;padding:.3rem 0;border:0;white-space:normal}
        .table-products td::before{display:inline-block;width:86px;margin:0;font-size:.66rem;letter-spacing:.04em}
        .table-products td:last-child::before{display:none}
        .table-products .prod-cell{padding:0 0 .55rem}
        .table-products .prod-cell::before{display:none}
        .table-products .prod-thumb{display:grid;grid-template-columns:54px minmax(0,1fr);align-items:center}
        .table-products .prod-thumb img{width:54px;height:54px}
        .table-products .prod-name{white-space:normal}
        .table-products td:last-child .btn{flex:1 1 auto}
    }
</style>
@endpush
<div class="page-head"><div><h1 class="title">Products</h1><p class="subtle">Manage the product catalog and inventory.</p></div><a class="btn btn-primary" href="{{ route('admin.products.create') }}">Add product</a></div>
<form class="toolbar" method="GET"><div class="field"><label>Search</label><input class="input" name="search" value="{{ request('search') }}"></div><div class="field"><label>Category</label><select class="select" name="category_id"><option value="">All</option>@foreach($categories as $c)<option value="{{ $c->id }}" @selected(request('category_id')==$c->id)>{{ $c->name }}</option>@endforeach</select></div><button class="btn btn-soft">Filter</button></form>
<div class="card table-wrap"><table class="table table-products"><thead><tr><th>Product</th><th>SKU</th><th>Category</th><th>Price</th><th>Stock</th><th>Hero</th><th>Status</th><th></th></tr></thead><tbody>@forelse($products as $p)<tr><td class="prod-cell"><span class="prod-thumb"><img src="{{ app(\App\Services\ProductImageResolver::class)->resolve($p->images->first()?->image_path ?? $p->image) }}" onerror="this.onerror=null;this.src='{{ app(\App\Services\ProductImageResolver::class)->placeholder() }}'" alt="" loading="lazy"><span class="prod-meta"><strong class="prod-name">{{ $p->name }}</strong><span class="prod-count">{{ $p->images_count }} image{{ $p->images_count==1?'':'s' }}</span></span></span></td><td>{{ $p->sku }}</td><td>{{ $p->category?->name }}</td><td>&#2547;{{ number_format((float)$p->effective_price,0) }}</td><td>{{ $p->stock }}</td><td>{{ $p->is_featured?'Yes':'No' }} · {{ $p->sort_order }}</td><td><span class="badge {{ $p->is_active?'badge-green':'badge-red' }}">{{ $p->is_active?'Active':'Inactive' }}</span></td><td><a class="btn btn-soft" href="{{ route('admin.products.edit',$p) }}">Edit</a><form method="POST" action="{{ route('admin.products.destroy',$p) }}" style="display:inline" onsubmit="return confirm('Permanently delete this product? All images and database records for this product will be removed.')">@csrf @method('DELETE')<button class="btn btn-danger" type="submit">Delete</button></form></td></tr>@empty<tr><td colspan="8">No products found.</td></tr>@endforelse</tbody></table></div>{{ $products->links() }}
@endsection
