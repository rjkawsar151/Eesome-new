@extends('layouts.admin')
@section('title','Coupons')
@section('heading','Coupons')
@section('content')
<div class="page-head"><div><h1 class="title">Coupons</h1><p class="subtle">Server-validated checkout discounts.</p></div><a class="btn btn-primary" href="{{ route('admin.coupons.create') }}">Add coupon</a></div>
<form class="toolbar" method="GET"><div class="field"><label>Search</label><input class="input" name="q" value="{{ request('q') }}" placeholder="Coupon code"></div><button class="btn btn-soft">Filter</button></form>
<div class="card table-wrap"><table class="table"><thead><tr><th>Code</th><th>Discount</th><th>Minimum</th><th>Usage</th><th>Expires</th><th>Status</th><th></th></tr></thead><tbody>
@forelse($coupons as $coupon)<tr><td><strong>{{ $coupon->code }}</strong></td><td>{{ $coupon->discount_type === 'percentage' ? rtrim(rtrim($coupon->discount_value,'0'),'.').'%' : '৳'.number_format((float)$coupon->discount_value,0) }}</td><td>৳{{ number_format((float)$coupon->min_order_amount,0) }}</td><td>{{ $coupon->used_count }} / {{ $coupon->usage_limit ?? '∞' }}</td><td>{{ $coupon->expiry_date?->format('d M Y') ?? 'Never' }}</td><td><span class="badge {{ $coupon->status ? 'badge-green' : 'badge-red' }}">{{ $coupon->status ? 'Active' : 'Inactive' }}</span></td><td><a class="btn btn-soft" href="{{ route('admin.coupons.edit',$coupon) }}">Edit</a><form method="POST" action="{{ route('admin.coupons.destroy',$coupon) }}" style="display:inline" onsubmit="return confirm('Delete this coupon?')">@csrf @method('DELETE')<button class="btn btn-danger">Delete</button></form></td></tr>
@empty<tr><td colspan="7">No coupons found.</td></tr>@endforelse
</tbody></table></div><div class="pagination">{{ $coupons->links() }}</div>
@endsection
