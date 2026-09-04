<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LegacyProductImageController extends Controller
{
    public function __invoke(string $filename): BinaryFileResponse
    {
        abort_unless(
            preg_match('/\A[a-zA-Z0-9._-]+\.(?:jpe?g|png|webp|gif)\z/i', $filename) === 1,
            404
        );

        $imagePath = null;

        foreach ([
            base_path('Uploads/products'),
            base_path('Uploads/products/products'),
            base_path('uploads/products'),
            base_path('uploads/products/products'),
            public_path('uploads/products'),
            public_path('Uploads/products'),
            storage_path('app/public/products'),
            storage_path('app/public/uploads/products'),
        ] as $directory) {
            $imageDirectory = realpath($directory);
            if ($imageDirectory === false) {
                continue;
            }

            $candidate = realpath($imageDirectory.DIRECTORY_SEPARATOR.$filename);
            if ($candidate !== false && dirname($candidate) === $imageDirectory && is_file($candidate)) {
                $imagePath = $candidate;
                break;
            }
        }

        abort_if($imagePath === null, 404);

        return response()->file($imagePath, [
            'Cache-Control' => 'public, max-age=86400',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
