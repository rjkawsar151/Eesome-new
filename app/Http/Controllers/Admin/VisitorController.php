<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VisitorController extends Controller
{
    public function index(Request $request)
    {
        $days = [1, 3, 7, 30];
        $counts = [];
        foreach ($days as $d) {
            $since = now()->subDays($d - 1)->startOfDay();
            $counts[$d] = [
                'views'   => PageView::where('created_at', '>=', $since)->count(),
                'visitors' => PageView::where('created_at', '>=', $since)->distinct('ip_address')->count('ip_address'),
            ];
        }

        $query = PageView::query();
        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(fn ($q) => $q->where('url', 'like', "%{$search}%")
                ->orWhere('ip_address', 'like', "%{$search}%")
                ->orWhere('referrer', 'like', "%{$search}%")
                ->orWhere('source', 'like', "%{$search}%"));
        }
        if ($request->filled('source') && $request->source !== 'all') {
            $query->where('source', $request->source);
        }
        if ($request->filled('range')) {
            $query->where('created_at', '>=', now()->subDays((int) $request->range - 1)->startOfDay());
        }

        $pageViews = $query->latest()->paginate(30)->withQueryString();

        $sourceBreakdown = PageView::select('source', DB::raw('COUNT(*) as total'), DB::raw('COUNT(DISTINCT ip_address) as visitors'))
            ->where('created_at', '>=', now()->subDays(29)->startOfDay())
            ->groupBy('source')
            ->orderByDesc('total')
            ->get();

        $topPages = PageView::select('url', DB::raw('COUNT(*) as total'), DB::raw('COUNT(DISTINCT ip_address) as visitors'))
            ->where('created_at', '>=', now()->subDays(29)->startOfDay())
            ->groupBy('url')
            ->orderByDesc('total')
            ->take(10)
            ->get();

        $sources = PageView::select('source')->distinct()->orderBy('source')->pluck('source');

        return view('admin.visitors.index', compact('counts', 'pageViews', 'sourceBreakdown', 'topPages', 'sources'));
    }
}
