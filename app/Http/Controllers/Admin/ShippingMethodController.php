<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingMethod;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ShippingMethodController extends Controller
{
    public function index()
    {
        return view('admin.shipping-methods.index', ['methods' => ShippingMethod::orderBy('sort_order')->paginate(30)]);
    }

    public function create()
    {
        return view('admin.shipping-methods.form', ['method' => new ShippingMethod]);
    }

    public function store(Request $request)
    {
        ShippingMethod::create($this->data($request));

        return redirect()->route('admin.shipping-methods.index')->with('success', 'Shipping method created.');
    }

    public function edit(ShippingMethod $shippingMethod)
    {
        return view('admin.shipping-methods.form', ['method' => $shippingMethod]);
    }

    public function update(Request $request, ShippingMethod $shippingMethod)
    {
        $shippingMethod->update($this->data($request, $shippingMethod));

        return back()->with('success', 'Shipping method updated.');
    }

    public function destroy(ShippingMethod $shippingMethod)
    {
        $shippingMethod->delete();

        return back()->with('success', 'Shipping method deleted.');
    }

    private function data(Request $request, ?ShippingMethod $method = null): array
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => ['required', 'alpha_dash', 'max:100', Rule::unique('shipping_methods')->ignore($method?->id)],
            'description' => 'nullable|string|max:1000',
            'charge_type' => 'required|in:flat,free,order_total_based',
            'base_charge' => 'required|numeric|min:0|max:999999',
            'minimum_order_amount' => 'nullable|numeric|min:0|max:99999999',
            'free_shipping_threshold' => 'nullable|numeric|min:0|max:99999999',
            'estimated_delivery_days' => 'nullable|integer|min:1|max:365',
            'sort_order' => 'required|integer|min:0',
        ]);
        $data['cod_available'] = $request->boolean('cod_available');
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
