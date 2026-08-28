@extends('layouts.app')

@section('title', ($success ? 'Payment Received' : 'Payment Not Completed') . ' — ' . config('app.name', 'Brickspoint Hotel'))

@section('content')
<div class="bg-gray-50 min-h-screen flex items-center justify-center px-4 py-16">
    <div class="w-full max-w-md rounded-2xl bg-white p-8 text-center shadow-sm ring-1 ring-gray-200">
        <div class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-full text-3xl text-white {{ $success ? 'bg-green-500' : 'bg-red-500' }}">
            {{ $success ? '✓' : '✕' }}
        </div>
        <h1 class="text-2xl font-bold text-gray-900">{{ $success ? 'Payment received' : 'Payment not completed' }}</h1>
        <p class="mt-2 text-sm text-gray-600">{{ $message }}</p>

        @if($success && isset($reservation) && $reservation)
            <p class="mt-4 text-sm text-gray-500">
                Booking Reference:
                <span class="font-mono font-semibold text-indigo-600">BR-{{ str_pad((string) $reservation->id, 5, '0', STR_PAD_LEFT) }}</span>
            </p>
            @if((float) $reservation->amount_paid > 0)
                <p class="mt-1 text-sm text-gray-500">
                    Amount paid:
                    <span class="font-semibold text-green-600">&#8358;{{ number_format($reservation->amount_paid, 2) }}</span>
                </p>
            @endif
        @endif

        <div class="mt-6 rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 text-xs text-gray-600">
            Paystack Transaction Ref: <strong class="break-all">{{ $reference }}</strong>
        </div>

        <a href="{{ route('home') }}" class="mt-6 inline-block w-full rounded-xl bg-gray-900 px-6 py-3 text-sm font-bold text-white transition hover:bg-black">
            Back to Brickspoint Hotel
        </a>
    </div>
</div>
@endsection
