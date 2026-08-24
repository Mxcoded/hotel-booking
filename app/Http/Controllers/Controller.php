<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Queue a staff notification email using the configured staff address.
     * Never interrupts the guest-facing flow on failure.
     */
    protected function notifyStaff(object $mailable, string $context): void
    {
        try {
            $staffEmail = setting('email') ?: config('mail.from.address');

            if (empty($staffEmail)) {
                Log::warning("Staff notification skipped for {$context}: no email address configured.");
                return;
            }

            Mail::to($staffEmail)->queue($mailable);
        } catch (\Throwable $e) {
            Log::error("Failed to queue {$context} notification: " . $e->getMessage());
        }
    }
}
