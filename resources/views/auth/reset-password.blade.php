@extends('layouts.guest')

@push('styles')
<style>
    .input-wrapper { position: relative; }
    .input-wrapper input { padding-right: 3rem; }
    .toggle-password {
        position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%);
        cursor: pointer; color: #9ca3af; transition: color 0.2s;
    }
    .toggle-password:hover { color: #f59e0b; }
    .toggle-password:focus { outline: none; color: #f59e0b; }
    .login-card { animation: slideUp 0.5s ease-out; }
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .brand-gradient { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
    .brand-gradient:hover { background: linear-gradient(135deg, #d97706 0%, #b45309 100%); }
</style>
@endpush

@section('body')
<div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:py-20 px-4 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <div class="text-center mb-8">
            <a href="{{ route('home') }}" class="inline-block" aria-label="Brickspoint Hotel Home">
                @if (setting('logo'))
                    <img src="{{ asset('storage/' . setting('logo')) }}" alt="Brickspoint Hotel Logo" class="h-16 w-auto mx-auto">
                @else
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full brand-gradient mx-auto mb-3">
                        <span class="text-3xl font-bold text-white">B</span>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900">Brickspoint</h2>
                    <p class="text-gray-600 tracking-widest uppercase text-sm">Admin Portal</p>
                @endif
            </a>
        </div>

        <div class="bg-white py-8 px-6 shadow-lg rounded-2xl sm:px-10 login-card">
            <h1 class="text-center text-2xl font-bold text-gray-900 mb-2">{{ __('Reset Password') }}</h1>
            <p class="text-center text-gray-600 mb-8">{{ __('Choose a new password for your account') }}</p>

            <form method="POST" action="{{ route('password.store') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div class="mb-5">
                    <x-input-label for="email" :value="__('Email Address')" />
                    <div class="mt-1">
                        <x-text-input
                            id="email"
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm py-3 px-4 transition-all duration-200"
                            type="email"
                            name="email"
                            :value="old('email')"
                            required
                            autofocus
                            autocomplete="email"
                        />
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div class="mb-5">
                    <x-input-label for="password" :value="__('New Password')" />
                    <div class="input-wrapper mt-1 relative">
                        <x-text-input
                            id="password"
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm py-3 px-4 transition-all duration-200 pr-12"
                            type="password"
                            name="password"
                            required
                            autocomplete="new-password"
                            placeholder="••••••••"
                        />
                        <button
                            type="button"
                            class="toggle-password absolute inset-y-0 right-0 flex items-center pr-3"
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

                <div class="mb-6">
                    <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                    <div class="mt-1">
                        <x-text-input
                            id="password_confirmation"
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm py-3 px-4 transition-all duration-200"
                            type="password"
                            name="password_confirmation"
                            required
                            autocomplete="new-password"
                            placeholder="••••••••"
                        />
                    </div>
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <button
                    type="submit"
                    class="w-full brand-gradient text-white font-semibold py-3 px-4 rounded-lg shadow-lg hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition-all duration-200 flex items-center justify-center gap-2"
                >
                    {{ __('Reset Password') }}
                </button>

                <input type="hidden" name="honeypot" value="" tabindex="-1" autocomplete="off" aria-hidden="true">
            </form>

            <div class="mt-6 text-center">
                <a href="{{ route('login') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 transition-colors">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    {{ __('Back to login') }}
                </a>
            </div>
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
});
</script>
@endpush