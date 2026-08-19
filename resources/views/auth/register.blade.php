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
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                </div>
                <div>
                    <h3 class="text-white font-semibold text-sm mb-0.5">Full Control Panel</h3>
                    <p class="text-white/70 text-xs leading-relaxed">Manage rooms, media, galleries, and pricing from one place.</p>
                </div>
            </div>
            <div class="flex items-start gap-3.5">
                <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-white/15 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div>
                    <h3 class="text-white font-semibold text-sm mb-0.5">Team Management</h3>
                    <p class="text-white/70 text-xs leading-relaxed">Multiple staff accounts with role-based access control.</p>
                </div>
            </div>
            <div class="flex items-start gap-3.5">
                <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-white/15 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <div>
                    <h3 class="text-white font-semibold text-sm mb-0.5">Analytics & Leads</h3>
                    <p class="text-white/70 text-xs leading-relaxed">Track visitors, WhatsApp leads, and guest feedback.</p>
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

            <h1 class="text-2xl font-bold text-gray-900 mb-1">Create your account</h1>
            <p class="text-gray-500 text-sm mb-7">Get started with Brickspoint Hotel admin panel.</p>

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

            <form method="POST" action="{{ route('register') }}" id="register-form">
                @csrf

                {{-- Name --}}
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">Full name</label>
                    <div class="auth-input-group">
                        <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <input
                            id="name"
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            required
                            autofocus
                            autocomplete="name"
                            placeholder="John Doe"
                            class="auth-input auth-input-icon"
                        >
                    </div>
                </div>

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
                            autocomplete="email"
                            placeholder="you@example.com"
                            class="auth-input auth-input-icon"
                        >
                    </div>
                </div>

                {{-- Password --}}
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
                    <div class="auth-input-group">
                        <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            autocomplete="new-password"
                            placeholder="••••••••"
                            class="auth-input auth-input-icon pr-11"
                        >
                        <button type="button" class="auth-pw-toggle" onclick="togglePassword(this)" tabindex="-1" aria-label="Show password">
                            <svg class="pw-show w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg class="pw-hide w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L3 3"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Confirm Password --}}
                <div class="mb-5">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">Confirm password</label>
                    <div class="auth-input-group">
                        <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            required
                            autocomplete="new-password"
                            placeholder="••••••••"
                            class="auth-input auth-input-icon pr-11"
                        >
                        <button type="button" class="auth-pw-toggle" onclick="togglePassword(this)" tabindex="-1" aria-label="Show password">
                            <svg class="pw-show w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg class="pw-hide w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L3 3"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Terms --}}
                <div class="flex items-start mb-6">
                    <input type="checkbox" id="terms" name="terms" required class="auth-checkbox mt-0.5">
                    <label for="terms" class="ml-2 text-sm text-gray-600">
                        I agree to the <a href="#" class="auth-link">Terms of Service</a> and <a href="#" class="auth-link">Privacy Policy</a>
                    </label>
                </div>

                {{-- Submit --}}
                <button type="submit" class="auth-btn auth-btn-primary" id="register-btn">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    <span>Register</span>
                </button>

                <input type="hidden" name="honeypot" value="" tabindex="-1" autocomplete="off" aria-hidden="true">
            </form>

            <div class="auth-divider"><span>or</span></div>

            <a href="{{ route('login') }}" class="auth-btn auth-btn-ghost text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                <span>Already have an account? Sign in</span>
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

document.getElementById('register-form')?.addEventListener('submit', function() {
    const btn = document.getElementById('register-btn');
    btn.disabled = true;
    btn.innerHTML = '<svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg><span>Creating account…</span>';
});
</script>
@endpush
