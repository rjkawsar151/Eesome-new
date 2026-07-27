@extends('layouts.admin')
@section('title',$coupon->exists ? 'Edit Coupon' : 'Add Coupon')
@section('heading','Coupons')
@section('content')
<h1 class="title">{{ $coupon->exists ? 'Edit coupon' : 'Add coupon' }}</h1>
<form class="card form-grid" style="margin-top:1rem" method="POST" action="{{ $coupon->exists ? route('admin.coupons.update',$coupon) : route('admin.coupons.store') }}">@csrf @if($coupon->exists)@method('PUT')@endif
<div class="field"><label>Code</label><input class="input" name="code" required value="{{ old('code',$coupon->code) }}"></div>
<div class="field"><label>Type</label><select class="select" name="discount_type"><option value="fixed" @selected(old('discount_type',$coupon->discount_type)==='fixed')>Fixed amount</option><option value="percentage" @selected(old('discount_type',$coupon->discount_type)==='percentage')>Percentage</option></select></div>
<div class="field"><label>Discount value</label><input class="input" type="number" min=".01" step=".01" name="discount_value" required value="{{ old('discount_value',$coupon->discount_value) }}"></div>
<div class="field"><label>Minimum order</label><input class="input" type="number" min="0" step=".01" name="min_order_amount" required value="{{ old('min_order_amount',$coupon->min_order_amount ?? 0) }}"></div>
<div class="field"><label>Expiry date</label><input class="input" type="date" name="expiry_date" value="{{ old('expiry_date',$coupon->expiry_date?->format('Y-m-d')) }}"></div>
<div class="field"><label>Usage limit</label><input class="input" type="number" min="1" name="usage_limit" value="{{ old('usage_limit',$coupon->usage_limit) }}"></div>
<div class="field full"><label><input type="checkbox" name="status" value="1" @checked(old('status',$coupon->exists ? $coupon->status : true))> Active</label></div>
<div class="full"><button class="btn btn-primary">Save coupon</button> <a class="btn btn-soft" href="{{ route('admin.coupons.index') }}">Cancel</a></div>
</form>
@endsection
