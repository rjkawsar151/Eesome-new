<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\PageView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Key metrics
        $totalOrders    = Order::count();
        $pendingOrders  = Order::where('order_status', 'Pending')->count();
        $totalRevenue   = Order::whereNotIn('order_status', ['Cancelled'])->sum('total_amount');
        $totalProducts  = Product::where('is_active', true)->count();
        $lowStockCount  = Product::where('is_active', true)->where('stock', '<=', 5)->where('is_preorder', false)->count();

        // User stats
        $totalCustomers    = User::where('role', 'user')->count();
        $totalAdmins       = User::where('role', 'admin')->count();
        $newUsersThisMonth = User::whereMonth('created_at', now()->month)->count();

        // Page visiting analytics - daily views (last 14 days)
        $dailyViews = PageView::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subDays(14))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        // Top visited pages
        $topPages = PageView::select('url', DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('url')
            ->orderByDesc('count')
            ->take(10)
            ->get();

        // Recent orders
        $recentOrders = Order::with('user')
            ->latest()
            ->take(10)
            ->get();

        // All users for user management section
        $users = User::orderByRaw("FIELD(role, 'admin', 'user')")
            ->orderBy('name')
            ->paginate(20);

        return view('admin.dashboard', compact(
            'totalOrders', 'pendingOrders', 'totalRevenue', 'totalProducts', 'lowStockCount',
            'totalCustomers', 'totalAdmins', 'newUsersThisMonth',
            'dailyViews', 'topPages', 'recentOrders', 'users'
        ));
    }
}
