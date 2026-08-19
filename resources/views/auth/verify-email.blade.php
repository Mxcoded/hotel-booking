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
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </div>
            <h3 class="text-white font-semibold text-lg mb-2">Verify Your Email</h3>
            <p class="text-white/70 text-sm leading-relaxed">We've sent a verification link to your email address. Click the link to activate your admin account.</p>
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

            <h1 class="text-2xl font-bold text-gray-900 mb-1">Verify your email</h1>
            <p class="text-gray-500 text-sm mb-7">
                Thanks for registering! Before getting started, please verify your email address by clicking the link we sent.
            </p>

            {{-- Session Status --}}
            @if (session('status'))
                <div class="auth-success">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('verification.send') }}" id="verify-form">
                @csrf
                <button type="submit" class="auth-btn auth-btn-primary" id="verify-btn">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <span>Resend verification email</span>
                </button>
            </form>

            <div class="mt-4 text-center">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-gray-500 hover:text-gray-700 font-medium transition-colors">
                        Log out
                    </button>
                </form>
            </div>

            <p class="text-center text-xs text-gray-400 mt-6">
                <a href="{{ route('home') }}" class="hover:text-gray-600 transition-colors">← Back to website</a>
            </p>
        </div>
    </main>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('verify-form')?.addEventListener('submit', function() {
    const btn = document.getElementById('verify-btn');
    btn.disabled = true;
    btn.innerHTML = '<svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg><span>Sending…</span>';
});
</script>
@endpush
