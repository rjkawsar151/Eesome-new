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
