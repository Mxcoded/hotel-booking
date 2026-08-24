<?php

namespace App\Mail;

use App\Models\Feedback;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FeedbackSubmitted extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Feedback $feedback)
    {
    }

    public function envelope(): Envelope
    {
        $author = $this->feedback->name ?: 'Anonymous Guest';

        return new Envelope(
            subject: "New Guest Feedback ({$this->feedback->rating}/5) — {$author}",
            replyTo: array_filter([$this->feedback->email]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.feedback-submitted',
        );
    }
}
