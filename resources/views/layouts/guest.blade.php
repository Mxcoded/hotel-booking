<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Brickspoint Hotel') }} — Admin Portal</title>

    @if (setting('favicon'))
        <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . setting('favicon')) }}">
    @endif

    @vite(['resources/css/app.css'])

    @stack('styles')

    <style>
        /* Auth page base styles */
        body { min-height: 100vh; margin: 0; font-family: system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif; }

        /* Split layout */
        .auth-split { display: grid; grid-template-columns: 1fr 1fr; min-height: 100vh; }
        @media (max-width: 1023px) {
            .auth-split { grid-template-columns: 1fr; }
            .auth-split .auth-showcase { display: none; }
        }

        /* Showcase panel */
        .auth-showcase {
            position: relative; display: flex; flex-direction: column; align-items: center; justify-content: center;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 50%, #b45309 100%);
            overflow: hidden; padding: 3rem;
        }
        .auth-showcase::before {
            content: ''; position: absolute; inset: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.06'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E") repeat;
        }
        .auth-showcase * { position: relative; z-index: 1; }

        /* Form panel */
        .auth-form-panel {
            display: flex; align-items: center; justify-content: center; padding: 2rem;
        }
        @media (min-width: 640px) { .auth-form-panel { padding: 3rem; } }

        /* Card */
        .auth-card {
            width: 100%; max-width: 28rem;
            background: #ffffff; border-radius: 1rem; padding: 2.5rem;
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.06), 0 8px 10px -6px rgba(0,0,0,0.04);
            animation: authCardIn 0.5s ease-out;
        }
        @keyframes authCardIn {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Inputs */
        .auth-input {
            display: block; width: 100%; padding: 0.75rem 1rem; font-size: 0.9375rem;
            border: 1.5px solid #d1d5db; border-radius: 0.625rem; background: #fafafa;
            transition: all 0.2s ease; outline: none; color: #111827;
        }
        .auth-input:focus { border-color: #f59e0b; background: #fff; box-shadow: 0 0 0 3px rgba(245,158,11,0.15); }
        .auth-input::placeholder { color: #9ca3af; }
        .auth-input-icon { padding-left: 2.75rem; }

        /* Input with icon */
        .auth-input-group { position: relative; }
        .auth-input-group .input-icon {
            position: absolute; left: 0.875rem; top: 50%; transform: translateY(-50%);
            width: 1.25rem; height: 1.25rem; color: #9ca3af; pointer-events: none;
            transition: color 0.2s;
        }
        .auth-input-group:focus-within .input-icon { color: #f59e0b; }

        /* Password toggle */
        .auth-pw-toggle {
            position: absolute; right: 0.5rem; top: 50%; transform: translateY(-50%);
            padding: 0.375rem; border-radius: 0.375rem; border: none; background: transparent;
            color: #9ca3af; cursor: pointer; transition: color 0.2s;
        }
        .auth-pw-toggle:hover { color: #f59e0b; }

        /* Button */
        .auth-btn {
            display: flex; align-items: center; justify-content: center; gap: 0.5rem; width: 100%;
            padding: 0.8125rem 1.5rem; font-size: 0.9375rem; font-weight: 600;
            border: none; border-radius: 0.625rem; cursor: pointer;
            transition: all 0.2s ease; outline: none;
        }
        .auth-btn-primary {
            color: #fff;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            box-shadow: 0 4px 12px rgba(245,158,11,0.35);
        }
        .auth-btn-primary:hover { box-shadow: 0 6px 20px rgba(245,158,11,0.45); transform: translateY(-1px); }
        .auth-btn-primary:active { transform: translateY(0); }
        .auth-btn-primary:focus-visible { box-shadow: 0 0 0 3px rgba(245,158,11,0.35); }
        .auth-btn-primary:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

        .auth-btn-ghost {
            color: #6b7280; background: transparent; padding: 0.5rem;
        }
        .auth-btn-ghost:hover { color: #374151; background: #f3f4f6; }

        /* Links */
        .auth-link { color: #d97706; font-weight: 500; text-decoration: none; transition: color 0.2s; }
        .auth-link:hover { color: #b45309; }

        /* Divider */
        .auth-divider { display: flex; align-items: center; gap: 1rem; margin: 1.5rem 0; }
        .auth-divider::before, .auth-divider::after {
            content: ''; flex: 1; height: 1px; background: #e5e7eb;
        }
        .auth-divider span { font-size: 0.8125rem; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em; }

        /* Error message */
        .auth-error {
            padding: 0.75rem 1rem; border-radius: 0.625rem; font-size: 0.875rem;
            background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; margin-bottom: 1rem;
        }
        .auth-error ul { margin: 0; padding-left: 1.25rem; }
        .auth-error li { margin: 0.125rem 0; }

        /* Success message */
        .auth-success {
            padding: 0.75rem 1rem; border-radius: 0.625rem; font-size: 0.875rem;
            background: #f0fdf4; border: 1px solid #bbf7d0; color: #16a34a; margin-bottom: 1rem;
        }

        /* Checkbox */
        .auth-checkbox {
            width: 1.125rem; height: 1.125rem; border-radius: 0.25rem;
            border: 1.5px solid #d1d5db; accent-color: #f59e0b; cursor: pointer;
        }

        /* Responsive card */
        @media (max-width: 1023px) {
            .auth-card { max-width: 26rem; margin: 0 auto; }
        }
        @media (max-width: 480px) {
            .auth-card { padding: 1.75rem 1.25rem; }
        }
    </style>
</head>
<body class="bg-gray-50 h-full">
    @yield('body')

    @stack('scripts')
</body>
</html>
