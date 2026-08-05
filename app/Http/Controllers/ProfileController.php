<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\OrderStatusHistory;
use App\Services\SiteSettingsRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    private const TABS = ['overview', 'orders', 'addresses', 'notifications', 'settings', 'support'];

    /**
     * Display the user's profile page.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $activeStatuses = ['awaiting', 'processing', 'confirmed', 'waiting_for_confirmation', 'shipped', 'in_transit'];
        $activeOrders = $user->orders()->with('items')->whereIn('order_status', $activeStatuses)->latest()->get();
        $recentOrders = $user->orders()->with('items')->latest()->limit(10)->get();
        $allOrders = $user->orders()->with('items')->latest()->get();

        $addresses = $user->orders()
            ->whereNotNull('shipping_address')
            ->orderByDesc('id')
            ->get(['customer_name', 'phone', 'district', 'thana', 'post_office', 'post_code', 'shipping_address'])
            ->unique(fn ($o) => implode('|', [$o->district, $o->thana, $o->post_office, $o->post_code, $o->shipping_address]))
            ->values();

        $notifications = OrderStatusHistory::query()
            ->whereIn('order_id', $user->orders()->pluck('id'))
            ->with('order:id,order_number')
            ->orderByDesc('created_at')
            ->limit(30)
            ->get();

        $settings = app(SiteSettingsRepository::class);
        $support = [
            'store_name' => $settings->get('store_name', 'EEsome'),
            'whatsapp' => $settings->get('contact_whatsapp'),
            'phone' => $settings->get('contact_phone'),
            'email' => $settings->get('support_email', $settings->get('contact_email')),
            'address' => $settings->get('business_address'),
            'facebook' => $settings->get('facebook_url'),
            'instagram' => $settings->get('instagram_url'),
        ];

        $tab = in_array(request('tab'), self::TABS, true) ? request('tab') : 'overview';

        $orderStats = [
            'total' => $user->orders()->count(),
            'active' => $activeOrders->count(),
            'delivered' => $user->orders()->where('order_status', 'delivered')->count(),
        ];

        return view('profile.edit', compact(
            'user',
            'activeOrders',
            'recentOrders',
            'allOrders',
            'addresses',
            'notifications',
            'support',
            'orderStats',
            'tab'
        ));
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
