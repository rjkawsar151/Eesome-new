<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $activeStatuses = ['awaiting', 'processing', 'confirmed', 'waiting_for_confirmation', 'shipped', 'in_transit'];
        $activeOrders = $user->orders()->with('items')->whereIn('order_status', $activeStatuses)->latest()->get();
        $recentOrders = $user->orders()->with('items')->latest()->limit(10)->get();

        return view('profile.edit', [
            'user' => $user,
            'activeOrders' => $activeOrders,
            'recentOrders' => $recentOrders,
            'orderStats' => [
                'total' => $user->orders()->count(),
                'active' => $activeOrders->count(),
                'delivered' => $user->orders()->where('order_status', 'delivered')->count(),
            ],
        ]);
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
