<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NavigationItem;
use App\Services\NavigationRepository;
use Illuminate\Http\Request;

class NavigationItemController extends Controller
{
    public function index()
    {
        return view('admin.navigation.index', ['items' => NavigationItem::orderBy('location')->orderBy('sort_order')->get()]);
    }

    public function create()
    {
        return view('admin.navigation.form', ['item' => new NavigationItem]);
    }

    public function store(Request $request)
    {
        NavigationItem::create($this->data($request));
        app(NavigationRepository::class)->clear();

        return redirect()->route('admin.navigation-items.index')->with('success', 'Navigation link created.');
    }

    public function edit(NavigationItem $navigationItem)
    {
        return view('admin.navigation.form', ['item' => $navigationItem]);
    }

    public function update(Request $request, NavigationItem $navigationItem)
    {
        $navigationItem->update($this->data($request));
        app(NavigationRepository::class)->clear();

        return back()->with('success', 'Navigation link updated.');
    }

    public function destroy(NavigationItem $navigationItem)
    {
        $navigationItem->delete();
        app(NavigationRepository::class)->clear();

        return back()->with('success', 'Navigation link deleted.');
    }

    private function data(Request $request): array
    {
        $data = $request->validate(['location' => 'required|in:header,footer', 'label' => 'required|string|max:100', 'url' => ['required', 'string', 'max:1000', 'regex:/^(\\/|https?:\\/\\/)/i'], 'sort_order' => 'required|integer|min:0']);
        $data['open_in_new_tab'] = $request->boolean('open_in_new_tab');
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
