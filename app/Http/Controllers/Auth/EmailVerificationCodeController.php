<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Services\EmailVerificationCodeService;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
class EmailVerificationCodeController extends Controller
{
    public function __invoke(Request $request, EmailVerificationCodeService $codes): RedirectResponse
    {
        $data = $request->validate(['code' => ['required', 'digits:6']]);
        if (! $codes->verify($request->user(), $data['code'])) return back()->withErrors(['code' => 'The verification code is invalid or has expired.']);
        event(new Verified($request->user()));
        $request->session()->forget('registration_verification_required');
        return redirect()->intended(RouteServiceProvider::HOME)->with('success', 'Your email address has been verified.');
    }
}