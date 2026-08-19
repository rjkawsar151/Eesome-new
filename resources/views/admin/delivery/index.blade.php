@extends('layouts.admin')
@section('title', 'Delivery & Location Settings')
@section('heading', 'Delivery & Location Settings')

@push('styles')
<style>
    .delivery-settings-grid {
        display: grid;
        grid-template-columns: 320px minmax(0, 1fr);
        gap: 1.5rem;
    }
    .div-card {
        margin-bottom: 1.25rem;
    }
    .div-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-bottom: 0.75rem;
        margin-bottom: 0.75rem;
        border-bottom: 1px solid var(--line);
    }
    .div-title {
        font-size: 1.1rem;
        font-weight: 800;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .district-table {
        width: 100%;
        border-collapse: collapse;
    }
    .district-table td {
        padding: 0.6rem 0.5rem;
        border-bottom: 1px dashed var(--line);
        vertical-align: middle;
    }
    .district-table tr:last-child td {
        border-bottom: 0;
    }
    .charge-input {
        width: 110px !important;
        text-align: right;
        font-weight: 700;
    }
    .toggle-switch {
        display: inline-flex;
        align-items: center;
        gap: 0.6rem;
        cursor: pointer;
        font-weight: 700;
    }
    .toggle-switch input {
        width: 1.2rem;
        height: 1.2rem;
        accent-color: var(--brand);
    }
    @media(max-width: 900px) {
        .delivery-settings-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="delivery-settings-grid">
    <!-- Left column: Free Delivery Settings -->
    <div>
        <div class="card">
            <h2 class="title" style="font-size:1.15rem;margin-bottom:1rem">Free Delivery Rules</h2>
            <form method="POST" action="{{ route('admin.delivery.update-settings') }}">
                @csrf
                @method('PUT')
                <div class="field full" style="margin-bottom:1.25rem">
                    <label class="toggle-switch">
                        <input type="checkbox" name="free_delivery_enabled" value="1" @checked($deliverySetting->free_delivery_enabled)>
                        <span>Enable Free Delivery</span>
                    </label>
                    <small class="subtle" style="display:block;margin-top:.25rem">Automatically apply ৳0 delivery charge when order subtotal meets the threshold.</small>
                </div>

                <div class="field full" style="margin-bottom:1.25rem">
                    <label for="free_delivery_threshold">Free Delivery Threshold (BDT)</label>
                    <input id="free_delivery_threshold" type="number" step="1" min="0" name="free_delivery_threshold" class="input" value="{{ old('free_delivery_threshold', $deliverySetting->free_delivery_threshold) }}" required>
                    <small class="subtle">Minimum order subtotal to unlock free delivery.</small>
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%">Save Threshold Settings</button>
            </form>
        </div>
    </div>

    <!-- Right column: Divisions & Districts Delivery Charges -->
    <div>
        <div class="card" style="margin-bottom:1rem;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem">
            <div>
                <h2 class="title" style="font-size:1.15rem;margin:0">District Delivery Charges</h2>
                <span class="subtle" style="font-size:.85rem">Configure delivery charges for each of the 64 districts across 8 Bangladesh divisions.</span>
            </div>
            <button type="submit" form="bulk-charge-form" class="btn btn-primary">Save All District Charges</button>
        </div>

        <form id="bulk-charge-form" method="POST" action="{{ route('admin.delivery.bulk-update') }}">
            @csrf
            @method('PUT')

            @foreach($divisions as $division)
                <div class="card div-card">
                    <div class="div-header">
                        <h3 class="div-title">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                            {{ $division->name }} Division
                        </h3>
                        <span class="badge badge-green">{{ $division->districts->count() }} Districts</span>
                    </div>

                    <table class="district-table">
                        <thead>
                            <tr style="text-align:left;font-size:.75rem;color:var(--muted);text-transform:uppercase">
                                <th>District</th>
                                <th style="text-align:right;padding-right:.5rem">Delivery Charge (BDT)</th>
                                <th style="text-align:center;width:90px">Status</th>
                                <th style="text-align:right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($division->districts as $district)
                                <tr>
                                    <td>
                                        <strong>{{ $district->name }}</strong>
                                        @if(in_array($district->name, ['Dhaka', 'Cumilla']))
                                            <span class="badge badge-yellow" style="margin-left:.35rem;font-size:.68rem">Special Initial Rate</span>
                                        @endif
                                    </td>
                                    <td style="text-align:right;padding-right:.5rem">
                                        <div style="display:inline-flex;align-items:center;gap:.3rem">
                                            <span>৳</span>
                                            <input type="number" step="1" min="0" name="charges[{{ $district->id }}]" value="{{ (int)$district->delivery_charge }}" class="input charge-input" required>
                                        </div>
                                    </td>
                                    <td style="text-align:center">
                                        <span class="badge {{ $district->status ? 'badge-green' : 'badge-red' }}">{{ $district->status ? 'Active' : 'Inactive' }}</span>
                                    </td>
                                    <td style="text-align:right">
                                        <form method="POST" action="{{ route('admin.delivery.update-district', $district->id) }}" style="display:inline-block">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="delivery_charge" value="{{ (int)$district->delivery_charge }}" class="single-charge-input-{{ $district->id }}">
                                            <button type="button" onclick="updateSingleDistrict('{{ $district->id }}', this)" class="btn btn-soft btn-sm">Update</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach

            <div style="margin-top:1.5rem;text-align:right">
                <button type="submit" class="btn btn-primary" style="padding:.75rem 1.5rem">Save All District Charges</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function updateSingleDistrict(districtId, btn) {
    const row = btn.closest('tr');
    const input = row.querySelector('input[name="charges[' + districtId + ']"]');
    if (!input) return;
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ url("admin/delivery/district") }}/' + districtId;
    
    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = '{{ csrf_token() }}';
    
    const method = document.createElement('input');
    method.type = 'hidden';
    method.name = '_method';
    method.value = 'PUT';

    const charge = document.createElement('input');
    charge.type = 'hidden';
    charge.name = 'delivery_charge';
    charge.value = input.value;

    const status = document.createElement('input');
    status.type = 'hidden';
    status.name = 'status';
    status.value = '1';

    form.appendChild(csrf);
    form.appendChild(method);
    form.appendChild(charge);
    form.appendChild(status);

    document.body.appendChild(form);
    form.submit();
}
</script>
@endpush
