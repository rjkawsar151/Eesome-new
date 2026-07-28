<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirectResponse;
class GoogleAuthController extends Controller
{
    public function redirect(): SymfonyRedirectResponse
    {
        return Socialite::driver('google')->redirectUrl(route('google.callback'))->redirect();
    }
    public function callback(CartService $cartService): RedirectResponse
    {
        try { $google = Socialite::driver('google')->redirectUrl(route('google.callback'))->user(); }
        catch (\Throwable $e) { report($e); return redirect()->route('login')->withErrors(['email' => 'Google sign-in could not be completed. Please try again.']); }
        if (! $google->getEmail()) return redirect()->route('login')->withErrors(['email' => 'Google did not provide an email address.']);
        $user = User::where('google_id', $google->getId())->orWhere('email', strtolower($google->getEmail()))->first();
        if (! $user) $user = new User(['email' => strtolower($google->getEmail()), 'password' => Hash::make(Str::random(40)), 'role' => 'customer']);
        $user->forceFill(['name' => $google->getName() ?: $google->getNickname() ?: 'Google user', 'google_id' => $google->getId(), 'profile_pic' => $user->profile_pic ?: $google->getAvatar(), 'email_verified_at' => $user->email_verified_at ?: now(), 'is_verified' => true])->save();
        Auth::login($user, true);
        request()->session()->regenerate();
        $cartService->mergeSessionCartIntoDb($user->id);
        return redirect()->intended(config('services.google.after_login', '/profile'));
    }
}