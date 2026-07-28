<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class PublicStorageImageController extends Controller
{
    private const ALLOWED_DIRECTORIES = [
        'blog/',
        'branding/',
        'categories/',
        'media/',
        'products/',
        'reviews/',
        'variants/',
    ];

    public function __invoke(string $path): Response
    {
        $path = ltrim(str_replace('\\', '/', $path), '/');

        abort_if(
            $path === ''
            || str_contains($path, '..')
            || ! collect(self::ALLOWED_DIRECTORIES)
                ->contains(fn (string $directory) => str_starts_with($path, $directory)),
            404
        );

        $disk = Storage::disk('public');

        abort_unless($disk->exists($path), 404);

        return $disk->response($path, null, [
            'Cache-Control' => 'public, max-age=31536000, immutable',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
