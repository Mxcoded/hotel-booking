@extends('layouts.app')

@section('title', 'Checkout — ' . config('app.name', 'Brickspoint Hotel'))

@section('content')
<div class="bg-gray-50 min-h-screen py-12">
    <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
        {{-- Page header --}}
        <div class="mb-8 text-center">
            <span class="inline-flex items-center gap-2 rounded-full bg-amber-100 px-4 py-1.5 text-sm font-semibold uppercase tracking-[0.2em] text-amber-700 mb-4"><i class="fas fa-lock"></i> Secure Payment</span>
            <h1 class="text-3xl font-bold text-gray-900">Complete Your Checkout</h1>
            <p class="mt-2 text-gray-600">Pay the balance to confirm your stay at Brickspoint Boutique Aparthotel.</p>
        </div>

        {{-- Order summary --}}
        <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-200">
            <div class="border-b border-gray-200 px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900">Booking Summary</h2>
                <p class="text-sm text-gray-500">Reference
                    <span class="font-mono font-semibold text-amber-600">BR-{{ str_pad((string) $reservation->id, 5, '0', STR_PAD_LEFT) }}</span>
                </p>
            </div>

            <div class="px-6 py-5">
                <dl class="space-y-4 text-sm">
                    <div class="flex items-center justify-between">
                        <dt class="text-gray-500">Room type</dt>
                        <dd class="font-medium text-gray-900">{{ $reservation->roomType->name ?? '—' }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-gray-500">Check-in</dt>
                        <dd class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($reservation->check_in)->format('D, M j Y') }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-gray-500">Check-out</dt>
                        <dd class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($reservation->check_out)->format('D, M j Y') }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-gray-500">Guests</dt>
                        <dd class="font-medium text-gray-900">{{ $reservation->guests }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-gray-500">Guest name</dt>
                        <dd class="font-medium text-gray-900">{{ $reservation->guest_name }}</dd>
                    </div>
                    @if($reservation->coupon_code)
                        <div class="flex items-center justify-between">
                            <dt class="text-gray-500">Promo code</dt>
                            <dd class="font-medium text-green-600">{{ $reservation->coupon_code }}</dd>
                        </div>
                    @endif
                </dl>

                <div class="mt-6 space-y-2 border-t border-dashed border-gray-200 pt-4 text-sm">
                    <div class="flex items-center justify-between">
                        <dt class="text-gray-500">Quoted total</dt>
                        <dd class="font-medium text-gray-900">&#8358;{{ number_format($reservation->quoted_total, 2) }}</dd>
                    </div>
                    @if((float) $reservation->amount_paid > 0)
                        <div class="flex items-center justify-between">
                            <dt class="text-gray-500">Already paid</dt>
                            <dd class="font-medium text-green-600">-&#8358;{{ number_format($reservation->amount_paid, 2) }}</dd>
                        </div>
                    @endif
                    <div class="flex items-center justify-between border-t border-gray-200 pt-3 text-base">
                        <dt class="font-semibold text-gray-900">Balance due</dt>
                        <dd class="font-bold text-amber-700">&#8358;{{ number_format($balance, 2) }}</dd>
                    </div>
                </div>
            </div>
        </div>

        {{-- Payment action --}}
        <form action="{{ route('pay.initiate', $reservation) }}" method="POST" class="mt-6">
            @csrf
            @include('partials._antibot')
            <button type="submit"
                class="flex w-full items-center justify-center gap-3 rounded-xl bg-amber-600 px-6 py-4 text-lg font-bold text-white shadow-sm transition hover:bg-amber-700 focus:outline-none focus:ring-4 focus:ring-amber-200">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h2m4 0h4M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                Pay &#8358;{{ number_format($balance, 2) }} securely
            </button>
            <p class="mt-3 text-center text-xs text-gray-500">
                Payments are processed securely by <span class="font-semibold">Paystack</span>. You will be redirected to complete the transaction.
            </p>
        </form>

        <div class="mt-6 text-center">
            <a href="{{ route('rooms') }}" class="text-sm font-medium text-amber-600 hover:text-amber-800">&larr; Continue browsing rooms</a>
        </div>
    </div>
</div>
@endsection
