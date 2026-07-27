<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BrandController extends Controller
{
    public function index()
    {
        return view('admin.brands.index', ['brands' => Brand::withCount('products')->orderBy('sort_order')->paginate(30)]);
    }

    public function create()
    {
        return view('admin.brands.form', ['brand' => new Brand]);
    }

    public function store(Request $request)
    {
        Brand::create($this->data($request));

        return redirect()->route('admin.brands.index')->with('success', 'Brand created.');
    }

    public function edit(Brand $brand)
    {
        return view('admin.brands.form', compact('brand'));
    }

    public function update(Request $request, Brand $brand)
    {
        $brand->update($this->data($request, $brand));

        return back()->with('success', 'Brand updated.');
    }

    public function destroy(Brand $brand)
    {
        if ($brand->products()->exists()) {
            return back()->with('error', 'Move products before deleting this brand.');
        } $brand->delete();

        return back()->with('success', 'Brand deleted.');
    }

    private function data(Request $request, ?Brand $brand = null): array
    {
        $data = $request->validate(['name' => 'required|string|max:255', 'slug' => ['required', 'alpha_dash', 'max:255', Rule::unique('brands')->ignore($brand?->id)], 'description' => 'nullable|string|max:2000', 'sort_order' => 'required|integer|min:0']);
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
