<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class MediaAsset extends Model
{
    protected $fillable = [
        'disk',
        'path',
        'original_name',
        'mime_type',
        'size',
        'alt_text',
        'uploaded_by',
    ];

    protected $appends = ['url', 'formatted_size'];

    public function getUrlAttribute(): string
    {
        return app(\App\Services\ProductImageResolver::class)->resolve($this->path);
    }

    public function getFormattedSizeAttribute(): string
    {
        $bytes = (int) $this->size;
        if ($bytes <= 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $i = (int) floor(log($bytes, 1024));
        $i = min($i, count($units) - 1);

        return round($bytes / pow(1024, $i), 1) . ' ' . $units[$i];
    }

    public function getUsageDetails(): array
    {
        $normalized = ltrim(str_replace('\\', '/', $this->path), '/');
        if (str_starts_with($normalized, 'storage/')) {
            $normalized = substr($normalized, 8);
        }
        $filename = basename($normalized);

        $candidates = array_unique(array_filter([
            $normalized,
            'storage/' . $normalized,
            '/' . $normalized,
            '/storage/' . $normalized,
            $filename,
            'products/' . $filename,
            'variants/' . $filename,
            'categories/' . $filename,
            'blog/' . $filename,
            'media/' . $filename,
            'branding/' . $filename,
            'reviews/' . $filename,
            'uploads/products/' . $filename,
        ]));

        $usage = [];

        // 1. Primary Product Images
        $products = Product::whereIn('image', $candidates)
            ->orWhere(function ($q) use ($filename) {
                if (strlen($filename) > 3) {
                    $q->where('image', 'like', '%/' . $filename);
                }
            })
            ->get(['id', 'name', 'sku']);

        foreach ($products as $p) {
            $skuText = $p->sku ? " (SKU: {$p->sku})" : '';
            $usage[] = [
                'type' => 'Product (Main Image)',
                'icon' => 'shopping-bag',
                'label' => $p->name . $skuText,
                'url' => route('admin.products.edit', $p->id),
            ];
        }

        // 2. Product Gallery Images
        $galleryImages = ProductImage::with('product')
            ->whereIn('image_path', $candidates)
            ->orWhere(function ($q) use ($filename) {
                if (strlen($filename) > 3) {
                    $q->where('image_path', 'like', '%/' . $filename);
                }
            })
            ->get();

        foreach ($galleryImages as $gi) {
            $pName = $gi->product?->name ?? "Product #{$gi->product_id}";
            $pSku = $gi->product?->sku ? " (SKU: {$gi->product->sku})" : '';
            $colorText = $gi->color_name ? " [Color: {$gi->color_name}]" : '';
            $usage[] = [
                'type' => 'Product Gallery Image',
                'icon' => 'images',
                'label' => $pName . $pSku . $colorText,
                'url' => $gi->product ? route('admin.products.edit', $gi->product->id) : null,
            ];
        }

        // 3. Product Variants
        $variants = ProductVariant::with('product')
            ->whereIn('image', $candidates)
            ->orWhere(function ($q) use ($filename) {
                if (strlen($filename) > 3) {
                    $q->where('image', 'like', '%/' . $filename);
                }
            })
            ->get();

        foreach ($variants as $v) {
            $pName = $v->product?->name ?? 'Product';
            $color = $v->color_name ?: ($v->color ?: ($v->name ?: 'Variant'));
            $vSku = $v->sku ? " [SKU: {$v->sku}]" : '';
            $usage[] = [
                'type' => 'Product Color Variant',
                'icon' => 'palette',
                'label' => "{$pName} — Color: {$color}{$vSku}",
                'url' => $v->product ? route('admin.products.edit', $v->product->id) : null,
            ];
        }

        // 4. Categories
        $categories = Category::whereIn('image', $candidates)
            ->orWhere(function ($q) use ($filename) {
                if (strlen($filename) > 3) {
                    $q->where('image', 'like', '%/' . $filename);
                }
            })
            ->get(['id', 'name']);

        foreach ($categories as $c) {
            $usage[] = [
                'type' => 'Category Banner',
                'icon' => 'folder',
                'label' => $c->name,
                'url' => route('admin.categories.edit', $c->id),
            ];
        }

        // 5. Blog Posts
        $posts = BlogPost::whereIn('image', $candidates)
            ->orWhere(function ($q) use ($filename) {
                if (strlen($filename) > 3) {
                    $q->where('image', 'like', '%/' . $filename);
                }
            })
            ->get(['id', 'title']);

        foreach ($posts as $b) {
            $usage[] = [
                'type' => 'Blog Post Featured Image',
                'icon' => 'file-text',
                'label' => $b->title,
                'url' => route('admin.blog.edit', $b->id),
            ];
        }

        // 6. Product Reviews
        $reviews = ProductReview::with('product')
            ->whereIn('image_path', $candidates)
            ->orWhere(function ($q) use ($filename) {
                if (strlen($filename) > 3) {
                    $q->where('image_path', 'like', '%/' . $filename);
                }
            })
            ->get();

        foreach ($reviews as $r) {
            $pName = $r->product?->name ?? 'Product';
            $usage[] = [
                'type' => 'Product Customer Review',
                'icon' => 'star',
                'label' => "Review by {$r->customer_name} on {$pName}",
                'url' => route('admin.reviews.index'),
            ];
        }

        // 7. Testimonials
        $testimonials = Testimonial::whereIn('image', $candidates)
            ->orWhere(function ($q) use ($filename) {
                if (strlen($filename) > 3) {
                    $q->where('image', 'like', '%/' . $filename);
                }
            })
            ->get(['id', 'name']);

        foreach ($testimonials as $t) {
            $usage[] = [
                'type' => 'Testimonial Avatar',
                'icon' => 'message-square',
                'label' => "Testimonial by {$t->name}",
                'url' => null,
            ];
        }

        // 8. Site Settings (e.g. Branding Logo or Favicon)
        $settings = SiteSetting::whereIn('setting_value', $candidates)
            ->orWhere(function ($q) use ($filename) {
                if (strlen($filename) > 3) {
                    $q->where('setting_value', 'like', '%/' . $filename);
                }
            })
            ->get(['id', 'setting_key']);

        foreach ($settings as $s) {
            $usage[] = [
                'type' => 'Site Branding / Settings',
                'icon' => 'settings',
                'label' => "Setting: {$s->setting_key}",
                'url' => route('admin.settings.edit'),
            ];
        }

        return $usage;
    }

    public function isInUse(): bool
    {
        return count($this->getUsageDetails()) > 0;
    }
}
