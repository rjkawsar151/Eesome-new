<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index()
    {
        return view('admin.categories.index', ['categories' => Category::withCount('products')->orderBy('sort_order')->paginate(30)]);
    }

    public function create()
    {
        return view('admin.categories.form', ['category' => new Category]);
    }

    public function store(Request $r)
    {
        $c = Category::create($this->data($r));
        $this->image($r, $c);

        return redirect()->route('admin.categories.index')->with('success', 'Category created.');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.form', compact('category'));
    }

    public function update(Request $r, Category $category)
    {
        $category->update($this->data($r, $category));
        $this->image($r, $category);

        return back()->with('success', 'Category updated.');
    }

    public function destroy(Category $category)
    {
        if ($category->products()->exists()) {
            return back()->with('error', 'Archive or move products first.');
        }$category->delete();

        return back()->with('success', 'Category deleted.');
    }

    private function data(Request $r, ?Category $c = null)
    {
        $d = $r->validate(['name' => 'required|string|max:255', 'slug' => ['required', 'alpha_dash', 'max:255', Rule::unique('categories')->ignore($c?->id)], 'sort_order' => 'required|integer|min:0', 'meta_title' => 'nullable|string|max:255', 'meta_description' => 'nullable|string|max:1000', 'is_active' => 'nullable|boolean', 'image_upload' => 'nullable|image|mimes:png,webp,jpg,jpeg|max:5120']);
        $d['is_active'] = $r->boolean('is_active');
        unset($d['image_upload']);

        return $d;
    }

    private function image(Request $r, Category $c)
    {
        if ($f = $r->file('image_upload')) {
            $old = $c->image;
            $c->update(['image' => $f->store('categories', 'public')]);
            if ($old && str_starts_with($old, 'categories/')) {
                Storage::disk('public')->delete($old);
            }
        }
    }
}
