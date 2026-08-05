<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $urls = [];

        $static = [
            ['loc' => url('/'), 'priority' => '1.0', 'changefreq' => 'daily'],
            ['loc' => route('products.index'), 'priority' => '0.6', 'changefreq' => 'daily'],
            ['loc' => route('about'), 'priority' => '0.3', 'changefreq' => 'monthly'],
        ];

        foreach ($static as $entry) {
            $urls[] = $entry;
        }

        foreach (Product::where('is_active', true)->orderBy('sort_order')->get() as $product) {
            $urls[] = [
                'loc' => route('products.show', $product->slug),
                'priority' => '0.8',
                'changefreq' => 'weekly',
                'lastmod' => $product->created_at ? \Illuminate\Support\Carbon::parse($product->created_at)->toAtomString() : null,
            ];
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

        foreach ($urls as $url) {
            $xml .= "  <url>\n";
            $xml .= '    <loc>'.htmlspecialchars($url['loc'], ENT_XML1, 'UTF-8')."</loc>\n";
            if (! empty($url['lastmod'])) {
                $xml .= '    <lastmod>'.$url['lastmod']."</lastmod>\n";
            }
            if (! empty($url['changefreq'])) {
                $xml .= '    <changefreq>'.$url['changefreq']."</changefreq>\n";
            }
            if (! empty($url['priority'])) {
                $xml .= '    <priority>'.$url['priority']."</priority>\n";
            }
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }
}
