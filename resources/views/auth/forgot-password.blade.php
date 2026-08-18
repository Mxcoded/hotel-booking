@extends('layouts.guest')

@push('styles')
<style>
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
            <h1 class="text-center text-2xl font-bold text-gray-900 mb-2">{{ __('Forgot Password') }}</h1>
            <p class="text-center text-gray-600 mb-8">{{ __('We\'ll email you a link to reset your password') }}</p>

            <x-auth-session-status class="mb-6" :status="session('status')" />

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="mb-6">
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
                            placeholder="admin@brickspoint.ng"
                        />
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <button
                    type="submit"
                    class="w-full brand-gradient text-white font-semibold py-3 px-4 rounded-lg shadow-lg hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition-all duration-200 flex items-center justify-center gap-2"
                >
                    {{ __('Email Password Reset Link') }}
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