<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ── Scheduled Maintenance ──────────────────────────────────

// Expire stale pending booking requests that were never confirmed or cancelled.
// The expiry window (in days) is configurable via the "pending_expiry_days" setting.
Schedule::call(function () {
    $graceDays = max(1, (int) (setting('pending_expiry_days', 3) ?: 3));

    $expired = \App\Models\Reservation::query()
        ->pending()
        ->where('created_at', '<', now()->subDays($graceDays))
        ->get();

    $count = 0;
    foreach ($expired as $reservation) {
        if ($reservation->markCancelled()) {
            $count++;
        }
    }

    if ($count > 0) {
        Log::info("Scheduled: expired {$count} stale pending booking request(s).");
    }
})->dailyAt('02:00')->name('expire-stale-pending');

// Send a daily morning briefing to staff: today's expected arrivals and departures.
Schedule::call(function () {
    $staffEmail = setting('email') ?: config('mail.from.address');
    if (empty($staffEmail)) {
        return;
    }

    $arrivals = \App\Models\Reservation::arriving()
        ->with('roomType')
        ->orderBy('check_in')
        ->get();

    $departures = \App\Models\Reservation::leaving()
        ->with('roomType')
        ->orderBy('check_out')
        ->get();

    try {
        Mail::to($staffEmail)->send(new \App\Mail\DailyOverview($arrivals, $departures));
    } catch (\Throwable $e) {
        Log::error('Failed to send daily overview email: ' . $e->getMessage());
    }
})->dailyAt('07:00')->name('daily-overview');

