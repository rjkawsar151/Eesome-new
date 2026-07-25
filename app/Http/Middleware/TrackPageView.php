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
        if ($request->isMethod('GET') && !$request->is('admin/*') && !$request->is('api/*')) {
            PageView::create([
                'ip_address' => $request->ip(),
                'url'        => $request->path(),
                'user_agent' => substr($request->userAgent() ?? '', 0, 500),
                'user_id'    => auth()->id(),
            ]);
        }

        return $response;
    }
}
