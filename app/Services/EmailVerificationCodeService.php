<?php
namespace App\Services;
use App\Models\OtpCode;
use App\Models\User;
use App\Notifications\EmailVerificationCode;
use Illuminate\Support\Facades\Hash;
class EmailVerificationCodeService
{
    public function send(User $user): void
    {
        $plainCode = (string) random_int(100000, 999999);
        $user->otpCodes()->delete();
        $user->otpCodes()->create(['code' => Hash::make($plainCode), 'expires_at' => now()->addMinutes(10)]);
        $user->notify(new EmailVerificationCode($plainCode));
    }
    public function verify(User $user, string $plainCode): bool
    {
        $otp = $user->otpCodes()->latest('id')->first();
        if (! $otp || ! $otp->expires_at || $otp->expires_at->isPast() || ! Hash::check($plainCode, $otp->code)) return false;
        $user->markEmailAsVerified();
        $user->forceFill(['is_verified' => true])->save();
        $user->otpCodes()->delete();
        return true;
    }
}