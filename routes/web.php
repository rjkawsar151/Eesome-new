<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\BlogController as AdminBlogController;
use App\Http\Controllers\Admin\BrandController as AdminBrandController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\CouponController as AdminCouponController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InventoryController as AdminInventoryController;
use App\Http\Controllers\Admin\MediaAssetController as AdminMediaAssetController;
use App\Http\Controllers\Admin\NavigationItemController as AdminNavigationItemController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PaymentMethodController as AdminPaymentMethodController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ProductVariantController as AdminProductVariantController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\ShippingMethodController as AdminShippingMethodController;
use App\Http\Controllers\Admin\TagController as AdminTagController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VisitorController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Storefront\AboutController;
use App\Http\Controllers\Storefront\CartController;
use App\Http\Controllers\Storefront\CheckoutController;
use App\Http\Controllers\Storefront\FacebookFeedController;
use App\Http\Controllers\Storefront\HomeController;
use App\Http\Controllers\Storefront\LegacyProductImageController;
use App\Http\Controllers\Storefront\OrderTrackerController;
use App\Http\Controllers\Storefront\ProductController;
use App\Http\Controllers\Storefront\ProductReviewController;
use App\Http\Controllers\Storefront\PublicStorageImageController;
use App\Http\Controllers\Storefront\SitemapController;
use App\Http\Controllers\Storefront\WishlistController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Storefront Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/feeds/facebook-catalog.xml', [FacebookFeedController::class, 'index'])->name('feeds.facebook');
Route::get('/about', AboutController::class)->name('about');
Route::get('/uploads/products/{filename}', LegacyProductImageController::class)
    ->where('filename', '[A-Za-z0-9._-]+\.(?:jpe?g|png|webp|gif)')
    ->name('legacy-product-images.show');
// Fallback for shared hosts where public/storage cannot be symlinked.
// When the symlink exists, the web server serves these files directly.
Route::get('/storage/{path}', PublicStorageImageController::class)
    ->where('path', '(?:blog|branding|categories|media|products|reviews|variants)/[A-Za-z0-9._/-]+')
    ->name('public-storage-images.show');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/suggestions', [ProductController::class, 'suggestions'])->middleware('throttle:30,1')->name('products.suggestions');
Route::post('/products/{product}/reviews', [ProductReviewController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('products.reviews.store');
Route::post('/products/{product}/wishlist', [WishlistController::class, 'toggle'])
    ->middleware('auth')
    ->name('products.wishlist.toggle');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');

// Order Tracking routes
Route::get('/track-order', [OrderTrackerController::class, 'index'])->middleware('throttle:15,1')->name('orders.track');
Route::post('/track-order', [OrderTrackerController::class, 'search'])->middleware('throttle:10,1')->name('orders.track.search');

// Cart routes (guest + auth)
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart', [CartController::class, 'store'])->middleware('throttle:30,1')->name('cart.store');
Route::patch('/cart/{line}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{line}', [CartController::class, 'destroy'])->name('cart.destroy');

// Checkout routes
Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout.show');
Route::get('/checkout/districts', [CheckoutController::class, 'getDistricts'])->name('checkout.districts');
Route::post('/checkout', [CheckoutController::class, 'store'])->middleware('throttle:10,1')->name('checkout.store');
Route::get('/checkout/success/{orderNumber}', [CheckoutController::class, 'success'])->name('checkout.success');

/*
|--------------------------------------------------------------------------
| Account Routes (Auth required)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        if (session('registration_verification_required') && ! auth()->user()->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        return auth()->user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('profile.edit');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Admin Routes (Auth + Admin Middleware)
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin', 'admin.activity'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/activity', [ActivityLogController::class, 'index'])->name('activity.index');
    Route::get('/visitors', [VisitorController::class, 'index'])->name('visitors.index');

    Route::get('/hero-products', [AdminProductController::class, 'hero'])->name('hero-products.edit');
    Route::put('/hero-products', [AdminProductController::class, 'updateHero'])->name('hero-products.update');

    Route::resource('products', AdminProductController::class)->except('show');
    Route::get('/products/slug-check', [AdminProductController::class, 'checkSlug'])->name('products.slug-check');
    Route::delete('/products/{product}/images/{image}', [AdminProductController::class, 'destroyImage'])->name('products.images.destroy');
    Route::resource('categories', AdminCategoryController::class)->except('show');
    Route::resource('brands', AdminBrandController::class)->except('show');
    Route::resource('tags', AdminTagController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::post('/products/{product}/variants', [AdminProductVariantController::class, 'store'])->name('products.variants.store');
    Route::put('/products/{product}/variants/{variant}', [AdminProductVariantController::class, 'update'])->name('products.variants.update');
    Route::delete('/products/{product}/variants/{variant}', [AdminProductVariantController::class, 'destroy'])->name('products.variants.destroy');
    Route::resource('reviews', AdminReviewController::class)->only(['index', 'update', 'destroy']);
    Route::resource('blog', AdminBlogController::class)->except('show')->parameters(['blog' => 'blog']);
    Route::get('/settings', [AdminSettingController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [AdminSettingController::class, 'update'])->name('settings.update');
    Route::get('/delivery', [\App\Http\Controllers\Admin\DeliverySettingController::class, 'index'])->name('delivery.index');
    Route::put('/delivery/settings', [\App\Http\Controllers\Admin\DeliverySettingController::class, 'updateSettings'])->name('delivery.update-settings');
    Route::put('/delivery/district/{district}', [\App\Http\Controllers\Admin\DeliverySettingController::class, 'updateDistrictCharge'])->name('delivery.update-district');
    Route::put('/delivery/bulk-update', [\App\Http\Controllers\Admin\DeliverySettingController::class, 'bulkUpdateDistricts'])->name('delivery.bulk-update');
    Route::resource('shipping-methods', AdminShippingMethodController::class)->except('show');
    Route::resource('payment-methods', AdminPaymentMethodController::class)->except('show');
    Route::resource('coupons', AdminCouponController::class)->except('show');
    Route::get('/inventory', [AdminInventoryController::class, 'index'])->name('inventory.index');
    Route::post('/inventory/{product}/adjust', [AdminInventoryController::class, 'adjust'])->name('inventory.adjust');
    Route::resource('navigation-items', AdminNavigationItemController::class)->except('show');
    Route::resource('media', AdminMediaAssetController::class)->only(['index', 'store', 'destroy'])->parameters(['media' => 'medium']);

    // Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::post('/orders/{order}/payment', [OrderController::class, 'updatePayment'])->name('orders.updatePayment');

    // Users
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/bulk-delete', [UserController::class, 'bulkDestroy'])->name('users.bulkDestroy');
});

require __DIR__.'/auth.php';
