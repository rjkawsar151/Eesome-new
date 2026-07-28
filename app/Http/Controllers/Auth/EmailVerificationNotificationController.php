<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(RouteServiceProvider::HOME);
        }

        try {
            app(\App\Services\EmailVerificationCodeService::class)->send($request->user());
            return back()->with('status', 'verification-code-sent');
        } catch (\Throwable $e) {
            report($e);
            return back()->withErrors(['email' => 'The verification code could not be sent. Please try again shortly.']);
        }
    }
}
