@extends('layouts.admin')
@section('title',$method->exists ? 'Edit Shipping Method' : 'Add Shipping Method')
@section('heading','Shipping methods')
@section('content')
<h1 class="title">{{ $method->exists ? 'Edit shipping method' : 'Add shipping method' }}</h1>
<form class="card form-grid" style="margin-top:1rem" method="POST" action="{{ $method->exists ? route('admin.shipping-methods.update',$method) : route('admin.shipping-methods.store') }}">@csrf @if($method->exists)@method('PUT')@endif
<div class="field"><label>Name</label><input class="input" name="name" required value="{{ old('name',$method->name) }}"></div>
<div class="field"><label>Code</label><input class="input" name="code" required value="{{ old('code',$method->code) }}"></div>
<div class="field full"><label>Description</label><textarea class="textarea" name="description">{{ old('description',$method->description) }}</textarea></div>
<div class="field"><label>Charge type</label><select class="select" name="charge_type">@foreach(['flat'=>'Flat charge','free'=>'Always free','order_total_based'=>'Order total based'] as $value=>$label)<option value="{{ $value }}" @selected(old('charge_type',$method->charge_type ?: 'flat')===$value)>{{ $label }}</option>@endforeach</select></div>
<div class="field"><label>Base charge (৳)</label><input class="input" type="number" min="0" step=".01" name="base_charge" required value="{{ old('base_charge',$method->base_charge ?? 0) }}"></div>
<div class="field"><label>Minimum order (optional)</label><input class="input" type="number" min="0" step=".01" name="minimum_order_amount" value="{{ old('minimum_order_amount',$method->minimum_order_amount) }}"></div>
<div class="field"><label>Free shipping threshold</label><input class="input" type="number" min="0" step=".01" name="free_shipping_threshold" value="{{ old('free_shipping_threshold',$method->free_shipping_threshold) }}"></div>
<div class="field"><label>Estimated delivery days</label><input class="input" type="number" min="1" name="estimated_delivery_days" value="{{ old('estimated_delivery_days',$method->estimated_delivery_days) }}"></div>
<div class="field"><label>Sort order</label><input class="input" type="number" min="0" name="sort_order" required value="{{ old('sort_order',$method->sort_order ?? 0) }}"></div>
<div class="field full"><label><input type="checkbox" name="cod_available" value="1" @checked(old('cod_available',$method->exists ? $method->cod_available : true))> Cash on delivery available</label><label><input type="checkbox" name="is_active" value="1" @checked(old('is_active',$method->exists ? $method->is_active : true))> Active</label></div>
<div class="full"><button class="btn btn-primary">Save shipping method</button> <a class="btn btn-soft" href="{{ route('admin.shipping-methods.index') }}">Cancel</a></div>
</form>
@endsection
