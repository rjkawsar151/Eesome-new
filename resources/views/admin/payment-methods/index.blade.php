@extends('layouts.admin')
@section('title','Payment Methods')
@section('heading','Payment methods')
@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem"><div><h1 class="title">Payment methods</h1><p class="subtle">Manage the options customers see at checkout.</p></div><a class="btn btn-primary" href="{{ route('admin.payment-methods.create') }}">Add method</a></div>
<div class="card table-wrap"><table class="table"><thead><tr><th>Name</th><th>Account</th><th>Transaction ID</th><th>Status</th><th></th></tr></thead><tbody>
@forelse($methods as $method)<tr><td><strong>{{ $method->name }}</strong><br><span class="subtle">{{ $method->code }}</span></td><td>{{ $method->account_number ?: '—' }}</td><td>{{ $method->requires_transaction_id ? 'Required' : 'Not required' }}</td><td><span class="badge {{ $method->is_active ? 'badge-green' : 'badge-red' }}">{{ $method->is_active ? 'Active' : 'Inactive' }}</span></td><td><a class="btn btn-soft" href="{{ route('admin.payment-methods.edit',$method) }}">Edit</a><form method="POST" action="{{ route('admin.payment-methods.destroy',$method) }}" style="display:inline" onsubmit="return confirm('Delete this payment method?')">@csrf @method('DELETE')<button class="btn btn-danger">Delete</button></form></td></tr>
@empty<tr><td colspan="5">No payment methods configured.</td></tr>@endforelse
</tbody></table></div><div class="pagination">{{ $methods->links() }}</div>
@endsection
