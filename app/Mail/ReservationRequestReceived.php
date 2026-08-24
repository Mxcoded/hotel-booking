<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReservationRequestReceived extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Reservation $reservation)
    {
    }

    public function envelope(): Envelope
    {
        $nights = (int) $this->reservation->check_in->diffInDays($this->reservation->check_out);

        return new Envelope(
            subject: "New Booking Request — {$this->reservation->guest_name} · {$this->reservation->roomType->name} ({$nights} night" . ($nights === 1 ? '' : 's') . ')',
            replyTo: [($this->reservation->guest_email ?: config('mail.from.address')) => $this->reservation->guest_name],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reservation-request',
            with: ['reservation' => $this->reservation],
        );
    }
}
