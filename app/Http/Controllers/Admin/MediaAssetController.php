<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\Category;
use App\Models\MediaAsset;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductReview;
use App\Models\ProductVariant;
use App\Models\SiteSetting;
use App\Models\Testimonial;
use App\Services\OptimizedImageStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class MediaAssetController extends Controller
{
    public function index(Request $request)
    {
        $this->syncSiteImages();

        $query = MediaAsset::query();

        if ($request->filled('q')) {
            $term = $request->string('q')->trim();
            $query->where(function ($q) use ($term) {
                $q->where('original_name', 'like', "%{$term}%")
                    ->orWhere('path', 'like', "%{$term}%")
                    ->orWhere('alt_text', 'like', "%{$term}%");
            });
        }

        $allAssets = $query->latest('id')->get();

        // Attach pre-calculated usage to all matching assets
        $allAssets->each(function ($asset) {
            $asset->usage = $asset->getUsageDetails();
            $asset->is_in_use = ! empty($asset->usage);
        });

        // Optional filter by usage status
        $usageFilter = $request->query('usage', 'all');
        $filtered = $allAssets;
        if ($usageFilter === 'in_use') {
            $filtered = $allAssets->filter(fn ($a) => $a->is_in_use);
        } elseif ($usageFilter === 'unused') {
            $filtered = $allAssets->filter(fn ($a) => ! $a->is_in_use);
        }

        $totalCount = $allAssets->count();
        $inUseCount = $allAssets->filter(fn ($a) => $a->is_in_use)->count();
        $unusedCount = $totalCount - $inUseCount;

        // Manual pagination for filtered collection
        $perPage = 24;
        $page = (int) $request->input('page', 1);
        $assets = new \Illuminate\Pagination\LengthAwarePaginator(
            $filtered->forPage($page, $perPage)->values(),
            $filtered->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.media.index', compact('assets', 'totalCount', 'inUseCount', 'unusedCount', 'usageFilter'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'file' => 'required|image|mimes:png,webp,jpg,jpeg,gif,svg,avif|max:5120',
            'alt_text' => 'nullable|string|max:255',
        ]);

        $file = $data['file'];
        $path = app(OptimizedImageStorage::class)->store($file, 'media');

        $size = Storage::disk('public')->exists($path) ? Storage::disk('public')->size($path) : $file->getSize();
        $mime = 'image/webp';

        MediaAsset::create([
            'disk' => 'public',
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $mime,
            'size' => (int) $size,
            'alt_text' => $data['alt_text'] ?? null,
            'uploaded_by' => auth()->id(),
        ]);

        return back()->with('success', 'Image uploaded successfully to the media library.');
    }

    public function usage(MediaAsset $medium)
    {
        $usage = $medium->getUsageDetails();

        return response()->json([
            'id' => $medium->id,
            'original_name' => $medium->original_name,
            'path' => $medium->path,
            'url' => $medium->url,
            'in_use' => ! empty($usage),
            'usage' => $usage,
        ]);
    }

    public function destroy(Request $request, MediaAsset $medium)
    {
        $usage = $medium->getUsageDetails();

        if (! empty($usage)) {
            $itemsList = collect($usage)->map(fn ($u) => "• [{$u['type']}] {$u['label']}")->implode("\n");
            $errorMessage = "Cannot delete '{$medium->original_name}'. It is currently in use across the following items:\n\n{$itemsList}";

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'in_use' => true,
                    'message' => "This image is currently in use and cannot be deleted.",
                    'usage' => $usage,
                ], 422);
            }

            return back()->with('error', $errorMessage);
        }

        // Delete from public storage
        app(OptimizedImageStorage::class)->delete($medium->path);

        // Also delete from legacy directories if present
        $filename = basename($medium->path);
        foreach ([
            base_path('Uploads/products/' . $filename),
            base_path('Uploads/products/products/' . $filename),
            base_path('uploads/products/' . $filename),
            public_path('uploads/products/' . $filename),
        ] as $legacyPath) {
            if (File::exists($legacyPath)) {
                @unlink($legacyPath);
            }
        }

        $medium->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Media file '{$medium->original_name}' deleted successfully.",
            ]);
        }

        return back()->with('success', "Media file '{$medium->original_name}' deleted successfully.");
    }

    /**
     * Auto-sync all images on disk and DB models into the media_assets table.
     */
    private function syncSiteImages(): void
    {
        try {
            $validExtensions = ['webp', 'jpg', 'jpeg', 'png', 'gif', 'svg', 'avif'];
            $discoveredPaths = [];

            // 1. Scan storage/app/public directory
            if (Storage::disk('public')->exists('')) {
                $allFiles = Storage::disk('public')->allFiles();
                foreach ($allFiles as $file) {
                    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                    if (in_array($ext, $validExtensions, true)) {
                        $normalized = ltrim(str_replace('\\', '/', $file), '/');
                        $size = (int) Storage::disk('public')->size($file);
                        $discoveredPaths[$normalized] = [
                            'disk' => 'public',
                            'path' => $normalized,
                            'original_name' => basename($normalized),
                            'mime_type' => 'image/' . ($ext === 'jpg' ? 'jpeg' : ($ext === 'svg' ? 'svg+xml' : $ext)),
                            'size' => $size,
                        ];
                    }
                }
            }

            // 2. Scan Uploads/products or public/uploads/products if present
            $legacyDirs = [
                base_path('Uploads/products'),
                base_path('Uploads/products/products'),
                base_path('uploads/products'),
                base_path('uploads/products/products'),
                public_path('uploads/products'),
                public_path('Uploads/products'),
            ];
            foreach ($legacyDirs as $dir) {
                if (File::isDirectory($dir)) {
                    $uploadFiles = File::allFiles($dir);
                    foreach ($uploadFiles as $file) {
                        $ext = strtolower($file->getExtension());
                        if (in_array($ext, $validExtensions, true)) {
                            $relPath = 'uploads/products/' . $file->getFilename();
                            $discoveredPaths[$relPath] = [
                                'disk' => 'public',
                                'path' => $relPath,
                                'original_name' => $file->getFilename(),
                                'mime_type' => 'image/' . ($ext === 'jpg' ? 'jpeg' : ($ext === 'svg' ? 'svg+xml' : $ext)),
                                'size' => $file->getSize(),
                            ];
                        }
                    }
                }
            }

            // 3. Scan DB models for any referenced paths
            $dbPaths = collect();
            $dbPaths = $dbPaths->merge(Product::whereNotNull('image')->pluck('image'));
            $dbPaths = $dbPaths->merge(ProductImage::whereNotNull('image_path')->pluck('image_path'));
            $dbPaths = $dbPaths->merge(ProductVariant::whereNotNull('image')->pluck('image'));
            $dbPaths = $dbPaths->merge(Category::whereNotNull('image')->pluck('image'));
            $dbPaths = $dbPaths->merge(BlogPost::whereNotNull('image')->pluck('image'));
            $dbPaths = $dbPaths->merge(ProductReview::whereNotNull('image_path')->pluck('image_path'));
            $dbPaths = $dbPaths->merge(Testimonial::whereNotNull('image')->pluck('image'));
            $dbPaths = $dbPaths->merge(SiteSetting::whereIn('setting_key', ['logo_path', 'favicon_path'])->pluck('setting_value'));

            foreach ($dbPaths->unique() as $rawPath) {
                if (! $rawPath || str_starts_with($rawPath, 'http://') || str_starts_with($rawPath, 'https://')) {
                    continue;
                }
                $normalized = ltrim(str_replace('\\', '/', $rawPath), '/');
                if (str_starts_with($normalized, 'storage/')) {
                    $normalized = substr($normalized, 8);
                }
                if (! isset($discoveredPaths[$normalized])) {
                    $size = 0;
                    $filename = basename($normalized);
                    if (Storage::disk('public')->exists($normalized)) {
                        $size = Storage::disk('public')->size($normalized);
                    } elseif (is_file(base_path('Uploads/products/' . $filename))) {
                        $size = filesize(base_path('Uploads/products/' . $filename));
                    } elseif (is_file(base_path('Uploads/products/products/' . $filename))) {
                        $size = filesize(base_path('Uploads/products/products/' . $filename));
                    } elseif (is_file(public_path('uploads/products/' . $filename))) {
                        $size = filesize(public_path('uploads/products/' . $filename));
                    }
                    $ext = strtolower(pathinfo($normalized, PATHINFO_EXTENSION)) ?: 'webp';
                    $discoveredPaths[$normalized] = [
                        'disk' => 'public',
                        'path' => $normalized,
                        'original_name' => basename($normalized),
                        'mime_type' => 'image/' . ($ext === 'jpg' ? 'jpeg' : ($ext === 'svg' ? 'svg+xml' : $ext)),
                        'size' => (int) $size,
                    ];
                }
            }

            // 4. Batch insert missing rows into media_assets
            $existingPaths = MediaAsset::pluck('path')->flip();
            $toInsert = [];
            $now = now();

            foreach ($discoveredPaths as $path => $data) {
                if (! isset($existingPaths[$path])) {
                    $toInsert[] = [
                        'disk' => $data['disk'],
                        'path' => $data['path'],
                        'original_name' => $data['original_name'],
                        'mime_type' => $data['mime_type'],
                        'size' => $data['size'],
                        'alt_text' => null,
                        'uploaded_by' => auth()->id(),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }

            if (! empty($toInsert)) {
                // Insert in chunks of 50 to avoid SQLite parameter limit
                foreach (array_chunk($toInsert, 50) as $chunk) {
                    MediaAsset::insert($chunk);
                }
            }
        } catch (\Throwable $e) {
            // Silently log or report sync errors so the media index page doesn't crash
            report($e);
        }
    }
}
