@extends('layouts.admin')
@section('title','Activity logs')
@section('heading','Activity logs')
@section('content')
<h1 class="title">Admin activity</h1><p class="subtle">A security audit trail of successful administrative changes. Passwords and secrets are never recorded.</p>
<form class="toolbar" method="GET"><div class="field"><label>Search</label><input class="input" name="search" value="{{ request('search') }}"></div><button class="btn btn-soft">Filter</button></form>
<div class="card table-wrap"><table class="table"><thead><tr><th>Time</th><th>Administrator</th><th>Action</th><th>Request</th><th>IP</th></tr></thead><tbody>@forelse($logs as $log)<tr><td>{{ $log->created_at?->format('d M Y H:i') }}</td><td>{{ $log->admin?->email ?? 'System' }}</td><td>{{ $log->action }}</td><td>{{ $log->description }}</td><td>{{ $log->ip_address }}</td></tr>@empty<tr><td colspan="5">No activity recorded.</td></tr>@endforelse</tbody></table></div>{{ $logs->links() }}
@endsection