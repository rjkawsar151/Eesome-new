@extends('layouts.admin')
@section('title',$method->exists ? 'Edit Payment Method' : 'Add Payment Method')
@section('heading','Payment methods')
@section('content')
<h1 class="title">{{ $method->exists ? 'Edit payment method' : 'Add payment method' }}</h1>
<form class="card form-grid" style="margin-top:1rem" method="POST" action="{{ $method->exists ? route('admin.payment-methods.update',$method) : route('admin.payment-methods.store') }}">@csrf @if($method->exists)@method('PUT')@endif
<div class="field"><label>Name</label><input class="input" name="name" required value="{{ old('name',$method->name) }}"></div>
<div class="field"><label>Code</label><input class="input" name="code" required value="{{ old('code',$method->code) }}"></div>
<div class="field"><label>Account name</label><input class="input" name="account_name" value="{{ old('account_name',$method->account_name) }}"></div>
<div class="field"><label>Account number</label><input class="input" name="account_number" value="{{ old('account_number',$method->account_number) }}"></div>
<div class="field full"><label>Customer instructions</label><textarea class="textarea" name="instructions">{{ old('instructions',$method->instructions) }}</textarea></div>
<div class="field"><label>Sort order</label><input class="input" type="number" min="0" name="sort_order" required value="{{ old('sort_order',$method->sort_order ?? 0) }}"></div>
<div class="field"><label><input type="checkbox" name="requires_transaction_id" value="1" @checked(old('requires_transaction_id',$method->requires_transaction_id))> Require transaction ID</label><label><input type="checkbox" name="is_active" value="1" @checked(old('is_active',$method->exists ? $method->is_active : true))> Active</label></div>
<div class="full"><button class="btn btn-primary">Save payment method</button> <a class="btn btn-soft" href="{{ route('admin.payment-methods.index') }}">Cancel</a></div>
</form>
@endsection
