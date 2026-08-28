<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Daily morning briefing for hotel staff listing today's expected arrivals
 * and departures.
 */
class DailyOverview extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly \Illuminate\Support\Collection $arrivals,
        public readonly \Illuminate\Support\Collection $departures,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Daily Overview — ' . now()->format('D, d M Y') . ' — Brickspoint Hotel',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.daily-overview',
            with: [
                'arrivals' => $this->arrivals,
                'departures' => $this->departures,
            ],
        );
    }
}
