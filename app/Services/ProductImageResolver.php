<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class ProductImageResolver
{
    private const PLACEHOLDER = 'images/handbag-placeholder.svg';

    public function resolve(?string $imagePath): string
    {
        if (empty($imagePath)) {
            return asset(self::PLACEHOLDER);
        }

        // Remote URL — return directly (temporary transition)
        if (str_starts_with($imagePath, 'http://') || str_starts_with($imagePath, 'https://')) {
            return $imagePath;
        }

        // Storage-managed path
        if (str_starts_with($imagePath, 'storage/')) {
            return asset($imagePath);
        }

        // Imported catalog images live in Uploads/products/products and are
        // exposed by a constrained storefront route.
        if (str_starts_with(str_replace('\\', '/', $imagePath), 'uploads/products/')) {
            return route('legacy-product-images.show', [
                'filename' => basename(str_replace('\\', '/', $imagePath)),
            ]);
        }

        // Already has products/ prefix — legacy relative path
        if (str_starts_with($imagePath, 'products/') || str_starts_with($imagePath, 'images/')) {
            return asset('storage/' . $imagePath);
        }

        // Fall back: treat as storage path
        return asset('storage/' . $imagePath);
    }

    public function placeholder(): string
    {
        return asset(self::PLACEHOLDER);
    }

    public function resolveAlt(?string $imagePath, string $productName = ''): string
    {
        if (empty($imagePath) || empty($productName)) {
            return 'Product image';
        }
        return $productName;
    }
}
