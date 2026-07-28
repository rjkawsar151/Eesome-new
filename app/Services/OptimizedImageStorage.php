<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class OptimizedImageStorage
{
    public function store(UploadedFile $file, string $directory, int $maxDimension = 1600, int $quality = 72): string
    {
        if (! extension_loaded('gd') || ! function_exists('imagewebp')) {
            throw new RuntimeException('The GD extension with WebP support is required for image uploads.');
        }

        $contents = file_get_contents($file->getRealPath());
        $source = $contents === false ? false : @imagecreatefromstring($contents);
        if ($source === false) {
            throw new RuntimeException('The uploaded image could not be processed.');
        }

        $width = imagesx($source);
        $height = imagesy($source);
        $scale = min(1, $maxDimension / max($width, $height));
        $targetWidth = max(1, (int) round($width * $scale));
        $targetHeight = max(1, (int) round($height * $scale));
        $target = imagecreatetruecolor($targetWidth, $targetHeight);
        imagealphablending($target, false);
        imagesavealpha($target, true);
        $transparent = imagecolorallocatealpha($target, 0, 0, 0, 127);
        imagefilledrectangle($target, 0, 0, $targetWidth, $targetHeight, $transparent);
        imagecopyresampled($target, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);

        $temporary = tmpfile();
        if ($temporary === false) {
            imagedestroy($source);
            imagedestroy($target);
            throw new RuntimeException('A temporary image file could not be created.');
        }
        $metadata = stream_get_meta_data($temporary);
        if (! imagewebp($target, $metadata['uri'], max(35, min(90, $quality)))) {
            fclose($temporary);
            imagedestroy($source);
            imagedestroy($target);
            throw new RuntimeException('The image could not be compressed.');
        }

        $path = trim($directory, '/').'/'.Str::uuid().'.webp';
        $stream = fopen($metadata['uri'], 'rb');
        Storage::disk('public')->put($path, $stream);
        if (is_resource($stream)) {
            fclose($stream);
        }
        fclose($temporary);
        imagedestroy($source);
        imagedestroy($target);

        return $path;
    }

    public function delete(?string $path): void
    {
        if (! is_string($path) || $path === '' || str_contains($path, '://')) {
            return;
        }
        $normalized = ltrim(str_replace('\\', '/', $path), '/');
        if (str_starts_with($normalized, 'storage/')) {
            $normalized = substr($normalized, 8);
        }
        $managedDirectories = ['products/', 'variants/', 'categories/', 'blog/', 'media/', 'branding/', 'reviews/'];
        if (collect($managedDirectories)->contains(fn ($directory) => str_starts_with($normalized, $directory))) {
            Storage::disk('public')->delete($normalized);
        }
    }
}