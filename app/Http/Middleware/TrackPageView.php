<?php

namespace App\Http\Middleware;

use App\Models\PageView;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackPageView
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only track GET requests to storefront (skip admin, api, assets)
        if ($request->isMethod('GET') && !$request->is('admin/*') && !$request->is('api/*') && !$request->is('sitemap.xml')) {
            $referrer = $request->headers->get('referer');
            PageView::create([
                'ip_address' => $request->ip(),
                'url'        => $request->path(),
                'user_agent' => substr($request->userAgent() ?? '', 0, 500),
                'user_id'    => auth()->id(),
                'referrer'   => $referrer ? substr($referrer, 0, 500) : null,
                'source'     => $this->sourceFromReferrer($referrer),
            ]);
        }

        return $response;
    }

    private function sourceFromReferrer(?string $referrer): string
    {
        if (! $referrer) {
            return 'direct';
        }

        $host = strtolower((string) parse_url($referrer, PHP_URL_HOST));
        $host = preg_replace('/^www\./', '', $host);

        return match (true) {
            $host === strtolower((string) parse_url(url('/'), PHP_URL_HOST)) => 'internal',
            str_contains($host, 'google.') => 'google',
            str_contains($host, 'facebook.') || str_contains($host, 'messenger.') || $host === 'm.me' => 'facebook',
            str_contains($host, 'instagram.') => 'instagram',
            str_contains($host, 'youtube.') => 'youtube',
            str_contains($host, 'twitter.') || $host === 'x.com' => 'twitter',
            str_contains($host, 'tiktok.') => 'tiktok',
            str_contains($host, 'whatsapp.') => 'whatsapp',
            str_contains($host, 'linkedin.') => 'linkedin',
            str_contains($host, 'bing.') => 'bing',
            $host !== '' => 'other',
            default => 'direct',
        };
    }
}
