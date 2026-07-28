<?php

namespace Tests\Feature;

use App\Http\Middleware\TrackPageView;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PublicStorageImageTest extends TestCase
{
    public function test_it_serves_an_uploaded_public_image_without_a_storage_symlink(): void
    {
        $this->withoutMiddleware(TrackPageView::class);
        Storage::fake('public');
        Storage::disk('public')->put('branding/logo.webp', 'image contents');

        $response = $this->get('/storage/branding/logo.webp');

        $response->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff');
        $this->assertStringContainsString('max-age=31536000', $response->headers->get('Cache-Control'));
        $this->assertSame('image contents', $response->streamedContent());
    }

    public function test_it_does_not_expose_files_outside_managed_image_directories(): void
    {
        $this->withoutMiddleware(TrackPageView::class);
        Storage::fake('public');
        Storage::disk('public')->put('private.txt', 'secret');

        $this->get('/storage/private.txt')->assertNotFound();
        $this->get('/storage/branding/../private.txt')->assertNotFound();
    }
}
