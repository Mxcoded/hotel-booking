<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>429 — Too Many Requests — {{ config('app.name', 'Brickspoint Hotel') }}</title>
    @if (setting('favicon'))
        <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . setting('favicon')) }}">
    @endif
    @vite(['resources/css/app.css'])
    <style>
        body { min-height: 100vh; margin: 0; font-family: system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif; }
        .error-split { display: grid; grid-template-columns: 1fr 1fr; min-height: 100vh; }
        @media (max-width: 1023px) { .error-split { grid-template-columns: 1fr; } .error-split .error-showcase { display: none; } }
        .error-showcase {
            position: relative; display: flex; flex-direction: column; align-items: center; justify-content: center;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 50%, #b45309 100%); overflow: hidden; padding: 3rem;
        }
        .error-showcase::before {
            content: ''; position: absolute; inset: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.06'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E") repeat;
        }
        .error-showcase * { position: relative; z-index: 1; }
        .error-form-panel { display: flex; align-items: center; justify-content: center; padding: 2rem; }
        @media (min-width: 640px) { .error-form-panel { padding: 3rem; } }
        .error-card {
            width: 100%; max-width: 28rem; background: #fff; border-radius: 1rem; padding: 2.5rem;
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.06), 0 8px 10px -6px rgba(0,0,0,0.04);
            animation: cardIn 0.5s ease-out; text-align: center;
        }
        @keyframes cardIn { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
        .error-btn {
            display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.75rem; font-size: 0.9375rem;
            font-weight: 600; border: none; border-radius: 0.625rem; cursor: pointer; transition: all 0.2s; text-decoration: none;
        }
        .error-btn-primary {
            color: #fff; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            box-shadow: 0 4px 12px rgba(245,158,11,0.35);
        }
        .error-btn-primary:hover { box-shadow: 0 6px 20px rgba(245,158,11,0.45); transform: translateY(-1px); }
        .error-btn-ghost { color: #6b7280; background: #f3f4f6; }
        .error-btn-ghost:hover { color: #374151; background: #e5e7eb; }
        @media (max-width: 1023px) { .error-card { max-width: 26rem; margin: 0 auto; } }
        @media (max-width: 480px) { .error-card { padding: 1.75rem 1.25rem; } }
    </style>
</head>
<body class="bg-gray-50 h-full">
    <div class="error-split">
        <aside class="error-showcase">
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
                <h3 class="text-white font-semibold text-lg mb-2">Slow Down</h3>
                <p class="text-white/70 text-sm leading-relaxed">You've been sending too many requests. Please wait a moment before trying again.</p>
            </div>
            <div class="mt-10 pt-6 border-t border-white/20 w-full max-w-sm">
                <p class="text-white/50 text-xs text-center">&copy; {{ date('Y') }} Brickspoint Hotel. All rights reserved.</p>
            </div>
        </aside>

        <main class="error-form-panel">
            <div class="error-card">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-yellow-50 mb-6">
                    <svg class="w-10 h-10 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <p class="text-6xl font-bold text-gray-200 mb-2">429</p>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Too Many Requests</h1>
                <p class="text-gray-500 text-sm mb-4 leading-relaxed">You've exceeded the request limit. Please wait a few moments before trying again.</p>
                <div id="countdown" class="text-amber-600 font-semibold text-sm mb-6"></div>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                    <button onclick="window.location.reload()" class="error-btn error-btn-primary" id="retry-btn">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Try Again
                    </button>
                    <a href="{{ route('home') }}" class="error-btn error-btn-ghost">Back to Home</a>
                </div>
            </div>
        </main>
    </div>
    <script>
        let seconds = 30;
        const countdown = document.getElementById('countdown');
        const btn = document.getElementById('retry-btn');
        function tick() {
            if (seconds <= 0) { countdown.textContent = ''; btn.disabled = false; btn.style.opacity = '1'; return; }
            countdown.textContent = 'You can retry in ' + seconds + ' seconds';
            btn.disabled = true; btn.style.opacity = '0.5';
            seconds--;
            setTimeout(tick, 1000);
        }
        tick();
    </script>
</body>
</html>
