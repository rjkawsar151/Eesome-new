@extends('layouts.admin')
@section('title','Orders')
@section('heading','Orders')
@section('content')
<h1 class="title">Orders</h1><p class="subtle">Search, filter, and review customer orders.</p>
<section class="card" style="margin-top:1.5rem">
<form class="toolbar" method="GET"><div class="field"><label>Search</label><input class="input" name="search" value="{{ request('search') }}" placeholder="Order, name, email…"></div><div class="field"><label>Status</label><select class="select" name="order_status"><option value="">All</option>@foreach(['awaiting','processing','shipped','in_transit','delivered','cancelled'] as $s)<option @selected(request('order_status')===$s)>{{ \Illuminate\Support\Str::headline($s) }}</option>@endforeach</select></div><div class="field"><label>Payment</label><select class="select" name="payment_status"><option value="">All</option>@foreach(['unpaid','pending','paid','failed','refunded'] as $s)<option @selected(request('payment_status')===$s)>{{ \Illuminate\Support\Str::headline($s) }}</option>@endforeach</select></div><button class="btn btn-primary">Filter</button></form>
<div class="table-wrap"><table class="table"><thead><tr><th>Order</th><th>Customer</th><th>Date</th><th>Payment</th><th>Status</th><th>Total</th><th></th></tr></thead><tbody>@forelse($orders as $order)<tr><td>{{ $order->order_number ?: '#'.$order->id }}</td><td>{{ $order->customer_name }}<br><small class="subtle">{{ $order->email }}</small></td><td>{{ $order->created_at?->format('d M Y') }}</td><td>{{ $order->payment_method }} · {{ $order->payment_status }}</td><td><span class="badge">{{ $order->order_status }}</span></td><td>৳{{ number_format((float)$order->total_amount,0) }}</td><td><a class="btn btn-soft" href="{{ route('admin.orders.show',$order) }}">View</a></td></tr>@empty<tr><td colspan="7">No matching orders.</td></tr>@endforelse</tbody></table></div><div class="pagination">{{ $orders->links() }}</div>
</section>
@endsection
