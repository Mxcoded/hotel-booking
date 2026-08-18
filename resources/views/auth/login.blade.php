@extends('layouts.guest')

@push('styles')
<style>
    :root {
        --brand-50: #fffbeb;
        --brand-100: #fef3c7;
        --brand-500: #f59e0b;
        --brand-600: #d97706;
        --brand-700: #b45309;
    }

    .brand-gradient {
        background: linear-gradient(135deg, var(--brand-500) 0%, var(--brand-600) 100%);
    }

    .brand-gradient-hover {
        background: linear-gradient(135deg, var(--brand-500) 0%, var(--brand-600) 100%);
    }

    .brand-gradient-hover:hover {
        background: linear-gradient(135deg, var(--brand-600) 0%, var(--brand-700) 100%);
    }

    .brand-text {
        background: linear-gradient(135deg, var(--brand-500) 0%, var(--brand-600) 100%);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
    }

    .showcase-gradient {
        background: linear-gradient(160deg, rgba(217, 119, 6, 0.96) 0%, rgba(180, 83, 9, 0.94) 35%, rgba(120, 53, 15, 0.96) 100%);
    }

    .form-card {
        animation: cardIn 0.5s cubic-bezier(0.22, 1, 0.36, 1) both;
    }

    @keyframes cardIn {
        from { opacity: 0; transform: translateY(24px) scale(0.98); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }

    .fade-in {
        animation: fadeIn 0.6s ease-out both;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .input-wrapper { position: relative; }

    .input-icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        width: 1.25rem;
        height: 1.25rem;
        color: #9ca3af;
        pointer-events: none;
        transition: color 0.2s;
    }

    .input-wrapper:focus-within .input-icon {
        color: var(--brand-600);
    }

    .input-wrapper.has-error .input-icon {
        color: #ef4444;
    }

    .toggle-password {
        position: absolute;
        right: 0.25rem;
        top: 50%;
        transform: translateY(-50%);
        width: 2.75rem;
        height: 2.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: #9ca3af;
        border-radius: 0.5rem;
        transition: color 0.2s, background-color 0.2s;
    }

    .toggle-password:hover {
        color: var(--brand-600);
        background-color: rgba(245, 158, 11, 0.08);
    }

    .toggle-password:focus-visible {
        outline: 2px solid var(--brand-500);
        outline-offset: 1px;
        color: var(--brand-600);
    }

    .btn-primary { position: relative; overflow: hidden; }

    .btn-primary:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }

    .spinner {
        display: inline-block;
        width: 1.25rem;
        height: 1.25rem;
        border: 2px solid rgba(255, 255, 255, 0.35);
        border-radius: 50%;
        border-top-color: #fff;
        animation: spin 0.7s linear infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    .remember-checkbox {
        accent-color: var(--brand-600);
    }

    .autofill-fix:-webkit-autofill,
    .autofill-fix:-webkit-autofill:hover,
    .autofill-fix:-webkit-autofill:focus {
        -webkit-box-shadow: 0 0 0 1000px #ffffff inset;
        -webkit-text-fill-color: #111827;
        transition: background-color 9999s ease-in-out 0s;
    }

    @media (prefers-reduced-motion: reduce) {
        .form-card, .fade-in {
            animation: none;
        }
    }

    @media (max-width: 1023px) {
        .showcase-panel {
            display: none;
        }
        .form-wrap {
            padding: 1.25rem;
        }
    }

    @media (min-width: 1024px) {
        .form-wrap {
            padding: 2.5rem;
        }
    }
</style>
@endpush

@section('body')
<div class="min-h-screen bg-gray-50 lg:bg-white flex flex-col lg:flex-row">
    {{-- Showcase Panel (desktop only) --}}
    <div class="showcase-panel hidden lg:flex lg:w-[46%] xl:w-1/2 relative overflow-hidden items-center justify-center">
        <div class="absolute inset-0 showcase-gradient">
            <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 20% 20%, #fff 1px, transparent 1px); background-size: 28px 28px;"></div>
            <div class="absolute -top-24 -right-24 w-80 h-80 rounded-full bg-white/10 blur-2xl"></div>
            <div class="absolute -bottom-32 -left-16 w-96 h-96 rounded-full bg-black/20 blur-3xl"></div>
        </div>

        <div class="relative z-10 text-white max-w-md mx-auto px-12 py-16 fade-in">
            <a href="{{ route('home') }}" class="inline-block mb-10" aria-label="Brickspoint Hotel Home">
                @if (setting('logo'))
                    <img src="{{ asset('storage/' . setting('logo')) }}" alt="Brickspoint Hotel Logo" class="h-14 w-auto">
                @else
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-14 h-14 rounded-2xl bg-white/15 backdrop-blur border border-white/20">
                            <span class="text-2xl font-extrabold">B</span>
                        </div>
                        <div>
                            <p class="text-xl font-bold leading-tight">Brickspoint</p>
                            <p class="text-xs tracking-[0.25em] uppercase opacity-80">Boutique Aparthotel</p>
                        </div>
                    </div>
                @endif
            </a>

            <h1 class="text-4xl font-extrabold leading-tight mb-4">
                Your Home<br>Away From Home
            </h1>
            <p class="text-lg text-white/85 mb-10 leading-relaxed">
                Sign in to manage rooms, gallery, messages, and more for Brickspoint Hotel — Wuse II, Abuja.
            </p>

            <div class="grid grid-cols-3 gap-4 pt-6 border-t border-white/15">
                <div>
                    <p class="text-2xl font-bold">50+</p>
                    <p class="text-xs text-white/70 mt-1">Rooms & Suites</p>
                </div>
                <div>
                    <p class="text-2xl font-bold">4.8★</p>
                    <p class="text-xs text-white/70 mt-1">Guest Rating</p>
                </div>
                <div>
                    <p class="text-2xl font-bold">24/7</p>
                    <p class="text-xs text-white/70 mt-1">Support</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Form Panel --}}
    <div class="flex-1 flex flex-col justify-center items-center px-4 sm:px-8 py-10 sm:py-16">
        <div class="w-full max-w-md form-card">

            {{-- Mobile-only brand header --}}
            <div class="lg:hidden text-center mb-8">
                <a href="{{ route('home') }}" class="inline-flex flex-col items-center gap-3" aria-label="Brickspoint Hotel Home">
                    @if (setting('logo'))
                        <img src="{{ asset('storage/' . setting('logo')) }}" alt="Brickspoint Hotel Logo" class="h-14 w-auto">
                    @else
                        <div class="flex items-center justify-center w-16 h-16 rounded-2xl brand-gradient shadow-lg">
                            <span class="text-3xl font-extrabold text-white">B</span>
                        </div>
                    @endif
                </a>
            </div>

            <div class="bg-white rounded-2xl lg:rounded-3xl shadow-xl shadow-gray-200/70 lg:shadow-2xl lg:shadow-gray-200/60 border border-gray-100 form-wrap">

                {{-- Desktop-only logo mark --}}
                <div class="hidden lg:flex items-center justify-center mb-6">
                    @if (setting('logo'))
                        <img src="{{ asset('storage/' . setting('logo')) }}" alt="Brickspoint Hotel Logo" class="h-12 w-auto">
                    @else
                        <div class="flex items-center justify-center w-12 h-12 rounded-xl brand-gradient shadow-md">
                            <span class="text-xl font-extrabold text-white">B</span>
                        </div>
                    @endif
                </div>

                <div class="text-center mb-8">
                    <h2 class="text-2xl sm:text-[1.65rem] font-bold text-gray-900 tracking-tight">{{ __('Welcome Back') }}</h2>
                    <p class="text-sm text-gray-500 mt-2">{{ __('Sign in to access the admin dashboard') }}</p>
                </div>

                <x-auth-session-status class="mb-6" :status="session('status')" />

                @if ($errors->any())
                    <div class="mb-6 rounded-xl bg-red-50 border border-red-200 p-4 fade-in" role="alert">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-red-500 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <p class="text-sm font-semibold text-red-800">{{ __('There was a problem signing you in.') }}</p>
                                <p class="text-sm text-red-600 mt-1">{{ __('Please check your credentials and try again.') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" id="login-form" novalidate>
                    @csrf

                    {{-- Email --}}
                    <div class="mb-5">
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('Email Address') }}</label>
                        <div class="input-wrapper {{ $errors->has('email') ? 'has-error' : '' }}">
                            <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                autofocus
                                autocomplete="username"
                                placeholder="you@example.com"
                                class="autofill-fix block w-full rounded-xl border {{ $errors->has('email') ? 'border-red-400' : 'border-gray-300' }} bg-white pl-11 pr-4 py-3.5 text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-amber-500 focus:ring-amber-500 focus:ring-2 focus:outline-none sm:text-sm transition-all duration-200"
                            />
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    {{-- Password --}}
                    <div class="mb-5">
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('Password') }}</label>
                        <div class="input-wrapper {{ $errors->has('password') ? 'has-error' : '' }}">
                            <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            <input
                                id="password"
                                type="password"
                                name="password"
                                required
                                autocomplete="current-password"
                                placeholder="••••••••"
                                class="autofill-fix block w-full rounded-xl border {{ $errors->has('password') ? 'border-red-400' : 'border-gray-300' }} bg-white pl-11 pr-12 py-3.5 text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-amber-500 focus:ring-amber-500 focus:ring-2 focus:outline-none sm:text-sm transition-all duration-200"
                            />
                            <button
                                type="button"
                                class="toggle-password"
                                id="toggle-password"
                                aria-label="Show password"
                                aria-controls="password"
                                tabindex="-1"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="eye-open">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="eye-closed">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L3 3m8.293 8.293l1.414 1.414"/>
                                </svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    {{-- Options row --}}
                    <div class="flex items-center justify-between mb-6">
                        <label for="remember_me" class="inline-flex items-center cursor-pointer select-none group">
                            <input
                                id="remember_me"
                                type="checkbox"
                                name="remember"
                                class="remember-checkbox h-[18px] w-[18px] rounded border-gray-300 focus:ring-amber-500"
                            >
                            <span class="ml-2.5 text-sm text-gray-600 group-hover:text-gray-800 transition-colors">{{ __('Remember me') }}</span>
                        </label>
                        @if (Route::has('password.request'))
                            <a
                                class="text-sm font-medium text-amber-600 hover:text-amber-700 transition-colors"
                                href="{{ route('password.request') }}"
                            >
                                {{ __('Forgot password?') }}
                            </a>
                        @endif
                    </div>

                    {{-- Submit --}}
                    <button
                        type="submit"
                        id="login-submit"
                        class="btn-primary brand-gradient-hover w-full text-white font-semibold py-3.5 px-4 rounded-xl shadow-lg shadow-amber-500/25 hover:shadow-xl hover:shadow-amber-500/30 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition-all duration-200 disabled:opacity-70 disabled:cursor-not-allowed flex items-center justify-center gap-2.5 text-[0.95rem]"
                    >
                        <span class="btn-text">{{ __('Sign in') }}</span>
                        <svg class="spinner hidden" aria-hidden="true"></svg>
                    </button>

                    <input type="hidden" name="honeypot" value="" tabindex="-1" autocomplete="off" aria-hidden="true">
                </form>

                {{-- Help link --}}
                <div class="mt-8 pt-6 border-t border-gray-100 text-center">
                    <p class="text-sm text-gray-500">
                        {{ __('Need help signing in?') }}
                        <a
                            href="https://wa.me/{{ setting('whatsapp_number', '+2348099999620') }}?text=Hi,%20I%20need%20help%20accessing%20the%20admin%20portal."
                            target="_blank"
                            rel="noopener noreferrer"
                            class="text-amber-600 hover:text-amber-700 font-medium ml-1 transition-colors"
                        >
                            {{ __('Contact support') }}
                        </a>
                    </p>
                </div>
            </div>

            {{-- Back to website (below card) --}}
            <div class="mt-7 text-center">
                <a
                    href="{{ route('home') }}"
                    class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 transition-colors group"
                >
                    <svg class="w-4 h-4 mr-1.5 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    {{ __('Back to website') }}
                </a>
            </div>

            {{-- Footer --}}
            <p class="mt-8 text-center text-xs text-gray-400">
                © {{ date('Y') }} {{ config('app.name', 'Brickspoint Hotel') }} · Admin Portal
            </p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('toggle-password');
    const passwordInput = document.getElementById('password');
    const eyeOpen = document.getElementById('eye-open');
    const eyeClosed = document.getElementById('eye-closed');

    if (toggleBtn && passwordInput) {
        toggleBtn.addEventListener('click', function() {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            eyeOpen.classList.toggle('hidden', isPassword);
            eyeClosed.classList.toggle('hidden', !isPassword);
            toggleBtn.setAttribute('aria-label', isPassword ? 'Hide password' : 'Show password');
        });
    }

    const form = document.getElementById('login-form');
    const submitBtn = document.getElementById('login-submit');
    const btnText = submitBtn?.querySelector('.btn-text');
    const spinner = submitBtn?.querySelector('.spinner');
    const emailInput = document.getElementById('email');
    const passwordInput2 = document.getElementById('password');

    if (form && submitBtn) {
        form.addEventListener('submit', function(e) {
            let firstInvalid = null;

            if (emailInput && !emailInput.value.trim()) {
                emailInput.classList.add('border-red-400');
                firstInvalid = firstInvalid || emailInput;
            }
            if (passwordInput2 && !passwordInput2.value) {
                passwordInput2.classList.add('border-red-400');
                firstInvalid = firstInvalid || passwordInput2;
            }

            if (firstInvalid) {
                e.preventDefault();
                firstInvalid.focus();
                return;
            }

            submitBtn.disabled = true;
            if (btnText) btnText.textContent = 'Signing in...';
            if (spinner) spinner.classList.remove('hidden');
        });

        [emailInput, passwordInput2].forEach(input => {
            if (!input) return;
            input.addEventListener('input', function() {
                this.classList.remove('border-red-400');
            });
        });
    }
});
</script>
@endpush