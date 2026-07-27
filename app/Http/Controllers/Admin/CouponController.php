<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CouponController extends Controller
{
    public function index(Request $request)
    {
        $coupons = Coupon::query()
            ->when($request->filled('q'), fn ($q) => $q->where('code', 'like', '%'.$request->string('q').'%'))
            ->latest()->paginate(30)->withQueryString();

        return view('admin.coupons.index', compact('coupons'));
    }

    public function create()
    {
        return view('admin.coupons.form', ['coupon' => new Coupon]);
    }

    public function store(Request $request)
    {
        Coupon::create($this->data($request));

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon created.');
    }

    public function edit(Coupon $coupon)
    {
        return view('admin.coupons.form', compact('coupon'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        $coupon->update($this->data($request, $coupon));

        return back()->with('success', 'Coupon updated.');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        return back()->with('success', 'Coupon deleted.');
    }

    private function data(Request $request, ?Coupon $coupon = null): array
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:100', Rule::unique('coupons')->ignore($coupon?->id)],
            'discount_type' => 'required|in:fixed,percentage',
            'discount_value' => 'required|numeric|min:0.01|max:9999999',
            'min_order_amount' => 'required|numeric|min:0|max:99999999',
            'expiry_date' => 'nullable|date',
            'usage_limit' => 'nullable|integer|min:1|max:99999999',
        ]);
        if ($data['discount_type'] === 'percentage' && (float) $data['discount_value'] > 100) {
            abort(422, 'Percentage discounts cannot exceed 100%.');
        }
        $data['code'] = strtoupper(trim($data['code']));
        $data['status'] = $request->boolean('status');

        return $data;
    }
}
