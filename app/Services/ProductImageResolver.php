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

    public function resolveVariantImage($product, $variant, int $index = 0): string
    {
        if (!$variant) {
            $imgPath = $product?->images?->first()?->image_path ?? $product?->image;
            return $this->resolve($imgPath);
        }

        $colorName = trim((string)($variant->color_name ?: $variant->name));
        $imgPath = $variant->image ?? $variant->image_path ?? null;

        if (!$imgPath && $product && $product->relationLoaded('images') && $product->images->isNotEmpty()) {
            // Match gallery image by color_name or alt_text
            $matched = $product->images->first(function ($img) use ($colorName) {
                return ($colorName !== '' && filled($img->color_name) && strcasecmp(trim((string)$img->color_name), $colorName) === 0)
                    || ($colorName !== '' && filled($img->alt_text) && strcasecmp(trim((string)$img->alt_text), $colorName) === 0);
            });
            $imgPath = $matched?->image_path;

            // Fallback by index if gallery images correspond 1-to-1 with variants
            if (!$imgPath && isset($product->images[$index]) && $product->images->count() >= ($product->activeVariants?->count() ?? 1)) {
                $imgPath = $product->images[$index]->image_path;
            }
        }

        if (!$imgPath && $product) {
            $imgPath = $product->images?->first()?->image_path ?? $product->image;
        }

        return $this->resolve($imgPath);
    }

    public function resolveAlt(?string $imagePath, string $productName = ''): string
    {
        if (empty($imagePath) || empty($productName)) {
            return 'Product image';
        }
        return $productName;
    }
}
