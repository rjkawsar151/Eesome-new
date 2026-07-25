@extends('layouts.admin')
@section('title','Dashboard')
@section('heading','Dashboard')
@section('content')
<h1 class="title">Store overview</h1><p class="subtle">A live snapshot of commerce, customers, and traffic.</p>
<div class="grid stats" style="margin:1.5rem 0">
@foreach([['Orders',$totalOrders],['Pending',$pendingOrders],['Revenue','৳'.number_format((float)$totalRevenue,0)],['Products',$totalProducts],['Low stock',$lowStockCount],['Customers',$totalCustomers],['Admins',$totalAdmins],['New users',$newUsersThisMonth]] as [$label,$value])<div class="card"><span class="subtle">{{ $label }}</span><div class="stat-value">{{ $value }}</div></div>@endforeach
</div>
<div class="grid two-col">
<section class="card"><div style="display:flex;justify-content:space-between"><h2>Recent orders</h2><a href="{{ route('admin.orders.index') }}">View all</a></div><div class="table-wrap"><table class="table"><thead><tr><th>Order</th><th>Customer</th><th>Status</th><th>Total</th></tr></thead><tbody>@forelse($recentOrders as $order)<tr><td><a href="{{ route('admin.orders.show',$order) }}">{{ $order->order_number ?: '#'.$order->id }}</a></td><td>{{ $order->customer_name }}</td><td><span class="badge">{{ $order->order_status }}</span></td><td>৳{{ number_format((float)$order->total_amount,0) }}</td></tr>@empty<tr><td colspan="4">No orders yet.</td></tr>@endforelse</tbody></table></div></section>
<aside class="card"><h2>Top pages · 30 days</h2>@forelse($topPages as $page)<div style="display:flex;justify-content:space-between;gap:1rem;padding:.55rem 0;border-bottom:1px solid var(--line)"><span style="overflow:hidden;text-overflow:ellipsis">{{ $page->url }}</span><strong>{{ $page->count }}</strong></div>@empty<p class="subtle">No page-view data yet.</p>@endforelse</aside>
</div>
@endsection
