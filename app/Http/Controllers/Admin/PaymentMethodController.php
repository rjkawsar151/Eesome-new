<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PaymentMethodController extends Controller
{
    public function index()
    {
        return view('admin.payment-methods.index', ['methods' => PaymentMethod::orderBy('sort_order')->paginate(30)]);
    }

    public function create()
    {
        return view('admin.payment-methods.form', ['method' => new PaymentMethod]);
    }

    public function store(Request $request)
    {
        PaymentMethod::create($this->data($request));

        return redirect()->route('admin.payment-methods.index')->with('success', 'Payment method created.');
    }

    public function edit(PaymentMethod $paymentMethod)
    {
        return view('admin.payment-methods.form', ['method' => $paymentMethod]);
    }

    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $paymentMethod->update($this->data($request, $paymentMethod));

        return back()->with('success', 'Payment method updated.');
    }

    public function destroy(PaymentMethod $paymentMethod)
    {
        $paymentMethod->delete();

        return back()->with('success', 'Payment method deleted.');
    }

    private function data(Request $request, ?PaymentMethod $method = null): array
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => ['required', 'alpha_dash', 'max:100', Rule::unique('payment_methods')->ignore($method?->id)],
            'account_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:100',
            'instructions' => 'nullable|string|max:2000',
            'sort_order' => 'required|integer|min:0',
        ]);
        $data['requires_transaction_id'] = $request->boolean('requires_transaction_id');
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
