@extends('layouts.admin')
@section('title','Shipping Methods')
@section('heading','Shipping methods')
@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem"><div><h1 class="title">Shipping methods</h1><p class="subtle">Control delivery pricing and availability.</p></div><a class="btn btn-primary" href="{{ route('admin.shipping-methods.create') }}">Add method</a></div>
<div class="card table-wrap"><table class="table"><thead><tr><th>Name</th><th>Charge</th><th>Free from</th><th>ETA</th><th>Status</th><th></th></tr></thead><tbody>
@forelse($methods as $method)<tr><td><strong>{{ $method->name }}</strong><br><span class="subtle">{{ $method->code }}</span></td><td>{{ $method->charge_type === 'free' ? 'Free' : '৳'.number_format((float)$method->base_charge, 0) }}</td><td>{{ $method->free_shipping_threshold ? '৳'.number_format((float)$method->free_shipping_threshold, 0) : '—' }}</td><td>{{ $method->estimated_delivery_days ? $method->estimated_delivery_days.' days' : '—' }}</td><td><span class="badge {{ $method->is_active ? 'badge-green' : 'badge-red' }}">{{ $method->is_active ? 'Active' : 'Inactive' }}</span></td><td><a class="btn btn-soft" href="{{ route('admin.shipping-methods.edit',$method) }}">Edit</a><form method="POST" action="{{ route('admin.shipping-methods.destroy',$method) }}" style="display:inline" onsubmit="return confirm('Delete this shipping method?')">@csrf @method('DELETE')<button class="btn btn-danger">Delete</button></form></td></tr>
@empty<tr><td colspan="6">No shipping methods configured.</td></tr>@endforelse
</tbody></table></div><div class="pagination">{{ $methods->links() }}</div>
@endsection
