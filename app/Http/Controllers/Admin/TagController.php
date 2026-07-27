<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TagController extends Controller
{
    public function index()
    {
        return view('admin.tags.index', ['tags' => Tag::withCount('products')->orderBy('name')->paginate(50)]);
    }

    public function store(Request $request)
    {
        Tag::create($request->validate(['name' => 'required|string|max:100', 'slug' => ['required', 'alpha_dash', 'max:100', Rule::unique('tags')]]));

        return back()->with('success', 'Tag created.');
    }

    public function update(Request $request, Tag $tag)
    {
        $tag->update($request->validate(['name' => 'required|string|max:100', 'slug' => ['required', 'alpha_dash', 'max:100', Rule::unique('tags')->ignore($tag->id)]]));

        return back()->with('success', 'Tag updated.');
    }

    public function destroy(Tag $tag)
    {
        $tag->products()->detach();
        $tag->delete();

        return back()->with('success', 'Tag deleted.');
    }
}
