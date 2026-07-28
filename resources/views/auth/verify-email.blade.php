<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">We sent a six-digit verification code to <strong>{{ auth()->user()->email }}</strong>. Enter it below. The code expires in 10 minutes.</div>
    @if(session('status') === 'verification-code-sent')<div class="mb-4 font-medium text-sm text-green-600">A new verification code has been sent.</div>@endif
    <form method="POST" action="{{ route('verification.code') }}">@csrf
        <x-input-label for="code" :value="__('Verification code')" />
        <x-text-input id="code" class="block mt-1 w-full text-center tracking-widest" type="text" name="code" inputmode="numeric" maxlength="6" autocomplete="one-time-code" required autofocus />
        <x-input-error :messages="$errors->get('code')" class="mt-2" />
        <x-primary-button class="mt-4 w-full justify-center">{{ __('Verify email') }}</x-primary-button>
    </form>
    <div class="mt-4 flex items-center justify-between gap-4"><form method="POST" action="{{ route('verification.send') }}">@csrf<button class="underline text-sm text-gray-600" type="submit">Resend code</button></form><form method="POST" action="{{ route('logout') }}">@csrf<button class="underline text-sm text-gray-600" type="submit">Log out</button></form></div>
</x-guest-layout>