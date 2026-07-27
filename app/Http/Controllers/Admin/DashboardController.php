<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PageView;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalOrders = Order::count();
        $ordersToday = Order::whereDate('created_at', today())->count();
        $pendingOrders = Order::where('order_status', 'awaiting')->count();
        $processingOrders = Order::where('order_status', 'processing')->count();
        $shippedOrders = Order::whereIn('order_status', ['shipped', 'in_transit'])->count();
        $totalRevenue = Order::where('order_status', '!=', 'cancelled')->sum('total_amount');
        $revenueToday = Order::where('order_status', '!=', 'cancelled')->whereDate('created_at', today())->sum('total_amount');
        $revenueThisMonth = Order::where('order_status', '!=', 'cancelled')->whereYear('created_at', now()->year)->whereMonth('created_at', now()->month)->sum('total_amount');
        $totalProducts = Product::where('is_active', true)->count();
        $lowStockCount = Product::where('is_active', true)->where('stock', '<=', 5)->where('is_preorder', false)->count();
        $pendingReviews = Review::where('status', 'pending')->count();
        $totalCustomers = User::where('role', 'user')->count();
        $totalAdmins = User::whereIn('role', ['admin', 'super admin'])->count();
        $newUsersThisMonth = User::whereYear('created_at', now()->year)->whereMonth('created_at', now()->month)->count();
        $dailyViews = PageView::select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))->where('created_at', '>=', now()->subDays(14))->groupBy(DB::raw('DATE(created_at)'))->orderBy('date')->get();
        $topPages = PageView::select('url', DB::raw('COUNT(*) as count'))->where('created_at', '>=', now()->subDays(30))->groupBy('url')->orderByDesc('count')->take(10)->get();
        $recentOrders = Order::with('user')->latest()->take(10)->get();
        $users = User::orderBy('name')->paginate(20);
        $alerts = collect([
            ['label' => 'Orders awaiting action', 'count' => $pendingOrders, 'route' => route('admin.orders.index', ['status' => 'awaiting'])],
            ['label' => 'Low-stock products', 'count' => $lowStockCount, 'route' => route('admin.inventory.index')],
            ['label' => 'Reviews awaiting moderation', 'count' => $pendingReviews, 'route' => route('admin.reviews.index', ['status' => 'pending'])],
        ])->filter(fn ($alert) => $alert['count'] > 0);

        return view('admin.dashboard', compact('totalOrders', 'ordersToday', 'pendingOrders', 'processingOrders', 'shippedOrders', 'totalRevenue', 'revenueToday', 'revenueThisMonth', 'totalProducts', 'lowStockCount', 'pendingReviews', 'totalCustomers', 'totalAdmins', 'newUsersThisMonth', 'dailyViews', 'topPages', 'recentOrders', 'users', 'alerts'));
    }
}
