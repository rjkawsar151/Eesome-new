<x-app-layout>
    @section('title', 'Sign In')

    <style>
        .login-page-wrapper {
            background-color: #fcfcfd;
            background-image: 
                radial-gradient(circle at 50% 15%, rgba(251, 207, 232, 0.35), transparent 45%),
                radial-gradient(circle at 85% 80%, rgba(252, 231, 243, 0.25), transparent 40%);
            min-height: calc(85vh - 100px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3.5rem 1rem;
            position: relative;
        }

        .login-card {
            background: #ffffff;
            border: 1px solid rgba(238, 240, 243, 0.9);
            border-radius: 1.75rem;
            box-shadow: 0 24px 60px -15px rgba(0, 0, 0, 0.06), 0 8px 24px -6px rgba(0, 0, 0, 0.02);
            width: 100%;
            max-width: 440px;
            padding: 2.75rem 2.5rem;
            margin: 0 auto;
            box-sizing: border-box;
            position: relative;
            isolation: isolate;
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 2rem;
            right: 2rem;
            height: 3px;
            background: linear-gradient(90deg, #f472b6, #db2777, #be185d);
            border-radius: 3px 3px 0 0;
        }

        .login-header {
            text-align: center;
            margin-bottom: 2.25rem;
        }

        .login-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.3rem 0.85rem;
            background: #fdf2f8;
            border: 1px solid #fce7f3;
            border-radius: 999px;
            color: #db2777;
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            margin-bottom: 1.25rem;
        }

        .login-avatar-container {
            width: 76px;
            height: 76px;
            margin: 0 auto 1.25rem auto;
            border-radius: 50%;
            background: linear-gradient(135deg, #ffffff, #fdf2f8);
            border: 2px solid #fbcfe8;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 24px rgba(219, 39, 119, 0.12), inset 0 2px 4px rgba(255, 255, 255, 0.8);
            position: relative;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .login-avatar-container:hover {
            transform: scale(1.04);
            box-shadow: 0 12px 28px rgba(219, 39, 119, 0.18), inset 0 2px 4px rgba(255, 255, 255, 0.8);
        }

        .login-avatar-icon {
            width: 38px;
            height: 38px;
            color: #db2777;
            stroke-width: 1.65;
        }

        .login-headline {
            font-family: 'Outfit', sans-serif;
            font-size: 1.75rem;
            font-weight: 800;
            color: #111827;
            margin: 0 0 0.4rem 0;
            letter-spacing: -0.03em;
            line-height: 1.2;
        }

        .login-subtitle {
            font-size: 0.875rem;
            color: #6b7280;
            margin: 0;
            line-height: 1.5;
        }

        .login-form {
            display: flex;
            flex-direction: column;
            gap: 1.35rem;
        }

        .login-field-group {
            display: flex;
            flex-direction: column;
            gap: 0.45rem;
        }

        .login-label {
            display: block;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #374151;
        }

        .login-input-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }

        .login-input-icon {
            position: absolute;
            left: 0.95rem;
            width: 20px;
            height: 20px;
            color: #9ca3af;
            pointer-events: none;
            transition: color 0.2s ease;
        }

        .login-input-wrap:focus-within .login-input-icon {
            color: #db2777;
        }

        .login-input {
            width: 100%;
            height: 50px;
            padding: 0 1rem 0 2.8rem;
            background-color: #f9fafb;
            border: 1.5px solid #e5e7eb;
            border-radius: 0.85rem;
            font-size: 0.925rem;
            color: #111827;
            outline: none;
            transition: all 0.2s ease;
            box-sizing: border-box;
        }

        .login-input:focus {
            background-color: #ffffff;
            border-color: #db2777;
            box-shadow: 0 0 0 4px rgba(219, 39, 119, 0.12);
        }

        .login-input::placeholder {
            color: #9ca3af;
        }

        .password-toggle-btn {
            position: absolute;
            right: 0.85rem;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            padding: 0.35rem;
            margin: 0;
            color: #9ca3af;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.5rem;
            transition: color 0.2s, background-color 0.2s;
            z-index: 2;
        }

        .password-toggle-btn:hover {
            color: #db2777;
            background-color: #fdf2f8;
        }

        .password-toggle-btn svg {
            width: 20px;
            height: 20px;
        }

        .login-row-between {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .login-remember {
            display: inline-flex;
            align-items: center;
            cursor: pointer;
            user-select: none;
            gap: 0.55rem;
        }

        .login-checkbox {
            width: 18px;
            height: 18px;
            accent-color: #db2777;
            border-radius: 5px;
            cursor: pointer;
        }

        .login-remember-text {
            font-size: 0.875rem;
            color: #4b5563;
            font-weight: 500;
        }

        .login-forgot-link {
            font-size: 0.8125rem;
            font-weight: 600;
            color: #db2777;
            text-decoration: none;
            transition: color 0.2s;
        }

        .login-forgot-link:hover {
            color: #be185d;
            text-decoration: underline;
        }

        .login-submit-btn {
            width: 100%;
            height: 52px;
            background: linear-gradient(135deg, #db2777 0%, #be185d 100%);
            color: #ffffff;
            border: none;
            border-radius: 0.85rem;
            font-size: 0.975rem;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            box-shadow: 0 10px 24px rgba(219, 39, 119, 0.28);
            transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
            margin-top: 0.35rem;
        }

        .login-submit-btn:hover {
            box-shadow: 0 14px 28px rgba(219, 39, 119, 0.36);
            transform: translateY(-2px);
        }

        .login-submit-btn:active {
            transform: translateY(0);
            box-shadow: 0 6px 16px rgba(219, 39, 119, 0.2);
        }

        .login-submit-arrow {
            width: 18px;
            height: 18px;
            transition: transform 0.2s ease;
        }

        .login-submit-btn:hover .login-submit-arrow {
            transform: translateX(3px);
        }

        .login-divider {
            position: relative;
            margin: 1.65rem 0 1.35rem 0;
            text-align: center;
        }

        .login-divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e5e7eb;
            z-index: 1;
        }

        .login-divider-span {
            position: relative;
            z-index: 2;
            background: #ffffff;
            padding: 0 0.85rem;
            font-size: 0.725rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #9ca3af;
        }

        .login-google-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            width: 100%;
            height: 48px;
            border: 1.5px solid #e5e7eb;
            border-radius: 0.85rem;
            background: #ffffff;
            color: #374151;
            font-size: 0.9rem;
            font-weight: 600;
            text-decoration: none;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
            transition: all 0.2s ease;
            box-sizing: border-box;
        }

        .login-google-btn:hover {
            background-color: #fdf2f8;
            border-color: #fbcfe8;
            color: #831843;
            transform: translateY(-1px);
        }

        .login-signup-box {
            margin-top: 2rem;
            padding-top: 1.35rem;
            border-top: 1px solid #f3f4f6;
            text-align: center;
            font-size: 0.875rem;
            color: #6b7280;
        }

        .login-signup-link {
            font-weight: 700;
            color: #db2777;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .login-signup-link:hover {
            color: #be185d;
            text-decoration: underline;
        }

        @media (max-width: 640px) {
            .login-page-wrapper {
                padding: 2rem 1rem;
            }
            .login-card {
                padding: 2rem 1.35rem;
                border-radius: 1.35rem;
            }
            .login-headline {
                font-size: 1.5rem;
            }
        }
    </style>

    <div class="login-page-wrapper">
        <div class="login-card">
            
            <!-- Card Header: Eyebrow Tag, Human Avatar, Headline, Subtitle -->
            <div class="login-header">
                <div class="login-eyebrow">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/></svg>
                    Welcome Back
                </div>

                <!-- Human Avatar Graphic -->
                <div class="login-avatar-container">
                    <svg class="login-avatar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                    </svg>
                </div>

                <h1 class="login-headline">Sign In to Your Account</h1>
                <p class="login-subtitle">Enter your credentials below to access your account & orders</p>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="login-form">
                @csrf

                <!-- Email Address -->
                <div class="login-field-group">
                    <label for="email" class="login-label">Email Address</label>
                    <div class="login-input-wrap">
                        <svg class="login-input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" /></svg>
                        <input id="email" 
                               type="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               required 
                               autofocus 
                               autocomplete="username" 
                               placeholder="name@example.com"
                               class="login-input">
                    </div>
                    <x-input-error :messages="$errors->get('email')" />
                </div>

                <!-- Password -->
                <div class="login-field-group">
                    <div class="login-row-between">
                        <label for="password" class="login-label">Password</label>
                        @if (Route::has('password.request'))
                            <a class="login-forgot-link" href="{{ route('password.request') }}">
                                Forgot password?
                            </a>
                        @endif
                    </div>
                    <div class="login-input-wrap">
                        <svg class="login-input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                        <input id="password" 
                               type="password" 
                               name="password" 
                               required 
                               autocomplete="current-password" 
                               placeholder="••••••••"
                               class="login-input"
                               style="padding-right: 2.8rem;">
                        <button type="button" 
                                class="password-toggle-btn" 
                                onclick="togglePasswordVisibility(this)" 
                                aria-label="Toggle password visibility">
                            <!-- Eye Open Icon -->
                            <svg class="eye-open-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <!-- Eye Closed / Slashed Icon -->
                            <svg class="eye-closed-icon" style="display: none;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.025 10.025 0 013.122-.563c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21f-9-9m-5.858-5.908L3 3" />
                            </svg>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password')" />
                </div>

                <!-- Remember Me -->
                <div class="login-row-between" style="margin-top: 0.25rem;">
                    <label for="remember_me" class="login-remember">
                        <input id="remember_me" type="checkbox" name="remember" value="1" class="login-checkbox">
                        <span class="login-remember-text">Remember me</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="login-submit-btn">
                    <span>Sign In</span>
                    <svg class="login-submit-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>

                <!-- Divider -->
                <div class="login-divider">
                    <span class="login-divider-span">Or continue with</span>
                </div>

                <!-- Google Button -->
                <a href="{{ route('google.redirect') }}" class="login-google-btn">
                    <svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true"><path fill="#4285F4" d="M21.6 12.2c0-.7-.1-1.4-.2-2H12v3.8h5.4a4.6 4.6 0 0 1-2 3v2.5h3.2c1.9-1.8 3-4.3 3-7.3Z"/><path fill="#34A853" d="M12 22c2.7 0 5-.9 6.6-2.4l-3.2-2.5c-.9.6-2 1-3.4 1a5.8 5.8 0 0 1-5.5-4H3.2v2.6A10 10 0 0 0 12 22Z"/><path fill="#FBBC05" d="M6.5 14a6 6 0 0 1 0-3.9V7.5H3.2a10 10 0 0 0 0 9.2L6.5 14Z"/><path fill="#EA4335" d="M12 6a5.4 5.4 0 0 1 3.8 1.5l2.9-2.8A9.7 9.7 0 0 0 3.2 7.5l3.3 2.6A5.8 5.8 0 0 1 12 6Z"/></svg>
                    <span>Continue with Google</span>
                </a>
            </form>


        </div>
    </div>

    <script>
        function togglePasswordVisibility(btn) {
            const passwordInput = document.getElementById('password');
            if (!passwordInput) return;

            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';

            const eyeOpen = btn.querySelector('.eye-open-icon');
            const eyeClosed = btn.querySelector('.eye-closed-icon');

            if (eyeOpen && eyeClosed) {
                eyeOpen.style.display = isPassword ? 'none' : 'block';
                eyeClosed.style.display = isPassword ? 'block' : 'none';
            }
        }
    </script>
</x-app-layout>
