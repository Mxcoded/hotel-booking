<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment @if($success) Received @else Not Completed @endif — Brickspoint Hotel</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; }
        body { background: #f4f4f5; display: flex; min-height: 100vh; align-items: center; justify-content: center; padding: 24px; }
        .card { background: #fff; border-radius: 12px; max-width: 480px; width: 100%; padding: 32px; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,.08); }
        .icon { width: 64px; height: 64px; border-radius: 50%; margin: 0 auto 20px; display: flex; align-items: center; justify-content: center; font-size: 32px; color: #fff; }
        .icon.success { background: #16a34a; }
        .icon.fail { background: #dc2626; }
        h1 { font-size: 22px; color: #111827; margin-bottom: 10px; }
        p { color: #6b7280; font-size: 14px; line-height: 1.6; margin-bottom: 18px; }
        .reference { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px; font-size: 13px; color: #374151; margin-bottom: 24px; word-break: break-all; }
        a.button { display: inline-block; background: #111827; color: #fff; text-decoration: none; padding: 12px 28px; border-radius: 8px; font-size: 14px; }
        a.button:hover { background: #1f2937; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon {{ $success ? 'success' : 'fail' }}">{{ $success ? '✓' : '✕' }}</div>
        <h1>{{ $success ? 'Payment received' : 'Payment not completed' }}</h1>
        <p>{{ $message }}</p>

        @if($success && isset($reservation) && $reservation)
            <p style="font-size:13px;">
                Reference: <strong>BR-{{ str_pad((string) $reservation->id, 5, '0', STR_PAD_LEFT) }}</strong>
            </p>
        @endif

        <div class="reference">
            Paystack Transaction Ref: <strong>{{ $reference }}</strong>
        </div>

        <a class="button" href="{{ route('home') }}">Back to Brickspoint Hotel</a>
    </div>
</body>
</html>
