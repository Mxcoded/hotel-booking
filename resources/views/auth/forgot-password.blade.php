@extends('layouts.guest')

@section('body')
<div class="auth-split">
    {{-- Left: Brand Showcase --}}
    <aside class="auth-showcase">
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-white/20 backdrop-blur-sm mb-6">
                <span class="text-4xl font-bold text-white tracking-tight">B</span>
            </div>
            <h1 class="text-3xl font-bold text-white mb-2">Brickspoint Hotel</h1>
            <p class="text-white/80 text-sm tracking-widest uppercase">Admin Portal</p>
        </div>

        <div class="max-w-sm text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-white/15 mb-5">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
            </div>
            <h3 class="text-white font-semibold text-lg mb-2">Password Recovery</h3>
            <p class="text-white/70 text-sm leading-relaxed">Enter your registered email address and we'll send you a secure link to reset your password.</p>
        </div>

        <div class="mt-10 pt-6 border-t border-white/20 w-full max-w-sm">
            <p class="text-white/50 text-xs text-center">© {{ date('Y') }} Brickspoint Hotel. All rights reserved.</p>
        </div>
    </aside>

    {{-- Right: Form --}}
    <main class="auth-form-panel">
        <div class="auth-card w-full">
            {{-- Logo on mobile --}}
            <div class="lg:hidden text-center mb-8">
                <a href="{{ route('home') }}" class="inline-block">
                    <div class="inline-flex items-center justify-center w-14 h-14 rounded-xl auth-btn-primary mb-3">
                        <span class="text-2xl font-bold text-white">B</span>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">Brickspoint</h2>
                    <p class="text-gray-500 text-xs tracking-widest uppercase">Admin Portal</p>
                </a>
            </div>

            <h1 class="text-2xl font-bold text-gray-900 mb-1">Forgot password?</h1>
            <p class="text-gray-500 text-sm mb-7">No worries, we'll send you reset instructions.</p>

            {{-- Session Status --}}
            @if (session('status'))
                <div class="auth-success">{{ session('status') }}</div>
            @endif

            {{-- Validation Errors --}}
            @if ($errors->any())
                <div class="auth-error">
                    <ul class="list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" id="forgot-form">
                @csrf

                <div class="mb-6">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email address</label>
                    <div class="auth-input-group">
                        <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/></svg>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="email"
                            placeholder="you@example.com"
                            class="auth-input auth-input-icon"
                        >
                    </div>
                </div>

                <button type="submit" class="auth-btn auth-btn-primary" id="forgot-btn">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <span>Send reset link</span>
                </button>

                <input type="hidden" name="honeypot" value="" tabindex="-1" autocomplete="off" aria-hidden="true">
            </form>

            <div class="mt-6 text-center">
                <a href="{{ route('login') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 transition-colors">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Back to sign in
                </a>
            </div>

            <p class="text-center text-xs text-gray-400 mt-4">
                <a href="{{ route('home') }}" class="hover:text-gray-600 transition-colors">← Back to website</a>
            </p>
        </div>
    </main>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('forgot-form')?.addEventListener('submit', function() {
    const btn = document.getElementById('forgot-btn');
    btn.disabled = true;
    btn.innerHTML = '<svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg><span>Sending…</span>';
});
</script>
@endpush
