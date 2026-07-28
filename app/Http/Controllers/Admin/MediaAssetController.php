<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MediaAsset;
use App\Services\OptimizedImageStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaAssetController extends Controller
{
    public function index(Request $request)
    {
        $assets = MediaAsset::query()->when($request->filled('q'), fn ($q) => $q->where('original_name', 'like', '%'.$request->string('q').'%'))->latest()->paginate(30)->withQueryString();

        return view('admin.media.index', compact('assets'));
    }

    public function store(Request $request)
    {
        $data = $request->validate(['file' => 'required|image|mimes:png,webp,jpg,jpeg,gif|max:5120', 'alt_text' => 'nullable|string|max:255']);
        $file = $data['file'];
        $path = app(OptimizedImageStorage::class)->store($file, 'media');
        MediaAsset::create(['disk' => 'public', 'path' => $path, 'original_name' => $file->getClientOriginalName(), 'mime_type' => 'image/webp', 'size' => Storage::disk('public')->size($path), 'alt_text' => $data['alt_text'] ?? null, 'uploaded_by' => auth()->id()]);

        return back()->with('success', 'Media uploaded.');
    }

    public function destroy(MediaAsset $medium)
    {
        $inUse = \App\Models\Product::where('image', $medium->path)->exists() || \App\Models\ProductImage::where('image_path', $medium->path)->exists() || \App\Models\Category::where('image', $medium->path)->exists() || \App\Models\BlogPost::where('image', $medium->path)->exists();
        if ($inUse) {
            return back()->with('error', 'This file is currently in use and cannot be deleted.');
        }
        app(OptimizedImageStorage::class)->delete($medium->path);
        $medium->delete();

        return back()->with('success', 'Media deleted.');
    }
}
