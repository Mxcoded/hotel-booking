@extends('layouts.guest')

@section('body')
<div class="auth-split">
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
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            </div>
            <h3 class="text-white font-semibold text-lg mb-2">Security Check</h3>
            <p class="text-white/70 text-sm leading-relaxed">For your protection, please confirm your password before making sensitive changes to your account.</p>
        </div>

        <div class="mt-10 pt-6 border-t border-white/20 w-full max-w-sm">
            <p class="text-white/50 text-xs text-center">&copy; {{ date('Y') }} Brickspoint Hotel. All rights reserved.</p>
        </div>
    </aside>

    <main class="auth-form-panel">
        <div class="auth-card w-full">
            <div class="lg:hidden text-center mb-8">
                <a href="{{ route('home') }}" class="inline-block">
                    <div class="inline-flex items-center justify-center w-14 h-14 rounded-xl auth-btn-primary mb-3">
                        <span class="text-2xl font-bold text-white">B</span>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">Brickspoint</h2>
                    <p class="text-gray-500 text-xs tracking-widest uppercase">Admin Portal</p>
                </a>
            </div>

            <h1 class="text-2xl font-bold text-gray-900 mb-1">Confirm your password</h1>
            <p class="text-gray-500 text-sm mb-7">Please enter your password to continue.</p>

            @if ($errors->any())
                <div class="auth-error">
                    <ul class="list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('password.confirm') }}" id="confirm-form">
                @csrf

                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
                    <div class="auth-input-group">
                        <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            autofocus
                            autocomplete="current-password"
                            placeholder="Enter your password"
                            class="auth-input auth-input-icon pr-11"
                        >
                        <button type="button" class="auth-pw-toggle" onclick="togglePassword(this)" tabindex="-1" aria-label="Show password">
                            <svg class="pw-show w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg class="pw-hide w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L3 3"/></svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="auth-btn auth-btn-primary" id="confirm-btn">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Confirm</span>
                </button>
            </form>

            <p class="text-center text-xs text-gray-400 mt-6">
                <a href="{{ route('home') }}" class="hover:text-gray-600 transition-colors">&larr; Back to website</a>
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

document.getElementById('confirm-form')?.addEventListener('submit', function() {
    const btn = document.getElementById('confirm-btn');
    btn.disabled = true;
    btn.innerHTML = '<svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg><span>Confirming...</span>';
});
</script>
@endpush
