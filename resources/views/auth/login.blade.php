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

        <div class="space-y-5 max-w-sm">
            <div class="flex items-start gap-3.5">
                <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-white/15 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <div>
                    <h3 class="text-white font-semibold text-sm mb-0.5">Secure & Private</h3>
                    <p class="text-white/70 text-xs leading-relaxed">Enterprise-grade security with encrypted sessions and CSRF protection.</p>
                </div>
            </div>
            <div class="flex items-start gap-3.5">
                <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-white/15 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/></svg>
                </div>
                <div>
                    <h3 class="text-white font-semibold text-sm mb-0.5">Room & Booking Management</h3>
                    <p class="text-white/70 text-xs leading-relaxed">Manage rooms, gallery, settings, and guest feedback from one place.</p>
                </div>
            </div>
            <div class="flex items-start gap-3.5">
                <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-white/15 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <div>
                    <h3 class="text-white font-semibold text-sm mb-0.5">Real-time Dashboard</h3>
                    <p class="text-white/70 text-xs leading-relaxed">Track visitors, leads, and performance metrics at a glance.</p>
                </div>
            </div>
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

            <h1 class="text-2xl font-bold text-gray-900 mb-1">Welcome back</h1>
            <p class="text-gray-500 text-sm mb-7">Sign in to your admin account to continue.</p>

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

            <form method="POST" action="{{ route('login') }}" id="login-form">
                @csrf

                {{-- Email --}}
                <div class="mb-4">
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

                {{-- Password --}}
                <div class="mb-4">
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-xs font-medium auth-link">Forgot password?</a>
                        @endif
                    </div>
                    <div class="auth-input-group">
                        <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            placeholder="••••••••"
                            class="auth-input auth-input-icon pr-11"
                        >
                        <button type="button" class="auth-pw-toggle" onclick="togglePassword(this)" tabindex="-1" aria-label="Show password">
                            <svg class="pw-show w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg class="pw-hide w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L3 3"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Remember --}}
                <div class="flex items-center mb-6">
                    <input type="checkbox" id="remember" name="remember" class="auth-checkbox" {{ old('remember') ? 'checked' : '' }}>
                    <label for="remember" class="ml-2 text-sm text-gray-600 cursor-pointer">Remember me</label>
                </div>

                {{-- Submit --}}
                <button type="submit" class="auth-btn auth-btn-primary" id="login-btn">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                    <span>Log in</span>
                </button>

                <input type="hidden" name="honeypot" value="" tabindex="-1" autocomplete="off" aria-hidden="true">
            </form>

            <div class="auth-divider"><span>or</span></div>

            <a href="{{ route('register') }}" class="auth-btn auth-btn-ghost text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                <span>Create a new account</span>
            </a>

            <p class="text-center text-xs text-gray-400 mt-6">
                <a href="{{ route('home') }}" class="hover:text-gray-600 transition-colors">← Back to website</a>
            </p>
        </div>
    </main>
</div>
@endsection

@push('scripts')
<script>
function togglePassword(btn) {
    const input = btn.closest('.auth-input-group').querySelector('input');
    const show = btn.querySelector('.pw-show');
    const hide = btn.querySelector('.pw-hide');
    const isPassword = input.type === 'password';
    input.type = isPassword ? 'text' : 'password';
    show.classList.toggle('hidden', isPassword);
    hide.classList.toggle('hidden', !isPassword);
    btn.setAttribute('aria-label', isPassword ? 'Hide password' : 'Show password');
}

document.getElementById('login-form')?.addEventListener('submit', function() {
    const btn = document.getElementById('login-btn');
    btn.disabled = true;
    btn.innerHTML = '<svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg><span>Signing in…</span>';
});
</script>
@endpush