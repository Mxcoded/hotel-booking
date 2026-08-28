<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Services\Paystack;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * Optional PaymentSettings allows a custom amount to be entered; by default we
     * charge the full quoted total so the reservation is settled in one go.
     *
     * GET /pay/{reservation}/pay
     */
    public function create(Request $request, Reservation $reservation, Paystack $paystack)
    {
        if (!$paystack->enabled()) {
            return redirect()->route('home')->with('error', 'Online payments are currently unavailable. Please contact us to complete your booking.');
        }

        if (!in_array($reservation->status, [Reservation::STATUS_PENDING, Reservation::STATUS_CONFIRMED], true)) {
            return redirect()->route('home')->with('error', 'This booking can no longer be paid for online.');
        }

        $alreadyPaid = $reservation->balanceDue();
        $amount = max(0, $alreadyPaid);

        if ($amount <= 0) {
            return redirect()->route('home')->with('success', 'This booking is already fully paid.');
        }

        $amountToCharge = filter_var($request->query('amount', $amount), FILTER_VALIDATE_FLOAT);
        $amountToCharge = min(max(0, (float) $amountToCharge), $amount);

        if ($amountToCharge <= 0) {
            return redirect()->route('home')->with('error', 'This booking balance cannot be paid online.');
        }

        $callback = url('/pay/callback');

        $result = $paystack->initialize([
            'email' => $reservation->guest_email ?: setting('email'),
            'amount' => $amountToCharge,
            'reference' => 'BR-' . str_pad((string) $reservation->id, 5, '0', STR_PAD_LEFT) . '-' . now()->format('His') . '-' . random_int(1000, 9999),
            'callback_url' => $callback,
            'metadata' => json_encode(['reservation_id' => $reservation->id]),
        ]);

        if (!$result['status'] || !$result['authorization_url']) {
            return redirect()->route('home')->with('error', 'Could not start the payment. Please contact the hotel.');
        }

        $reservation->update([
            'payment_reference' => $result['reference'] ?? $reservation->payment_reference,
            'payment_method' => 'paystack',
        ]);

        return redirect()->away($result['authorization_url']);
    }

    /**
     * GET /pay/callback — Paystack redirects the guest here after a payment attempt.
     */
    public function callback(Request $request, Paystack $paystack)
    {
        $reference = $request->query('reference');

        if (!$reference) {
            return redirect()->route('home')->with('error', 'No payment reference was returned by Paystack.');
        }

        $result = $paystack->verify($reference);

        if (!$result['status'] || !$result['paid']) {
            Log::warning('Paystack callback for a non-successful transaction', $result);
            return view('payments.result', [
                'success' => false,
                'reference' => $reference,
                'message' => $result['message'] ?? 'The payment was not completed.',
            ]);
        }

        $reservation = Reservation::where('payment_reference', $reference)->first();

        if (!$reservation) {
            $reservation = $this->resolveReservationFromReference($reference);
        }

        if ($reservation) {
            $reservation->recordPayment($result['amount'], 'paystack', $reference);
        }

        return view('payments.result', [
            'success' => true,
            'reference' => $reference,
            'reservation' => $reservation,
            'message' => 'Thank you! Your payment was received successfully.',
        ]);
    }

    /**
     * POST /pay/webhook — server-to-server notification from Paystack.
     */
    public function webhook(Request $request, Paystack $paystack)
    {
        $signature = $request->header('x-paystack-signature');
        if ($signature && hash_equals(hash_hmac('sha512', $request->getContent(), (string) ($paystack->secretKey() ?: '')), $signature)) {
            // Signature OK — fall through to process.
        } else {
            // No/incorrect signature: only honor verified callbacks from the safe path.
            return response()->json(['status' => false], 400);
        }

        $event = $request->input('event');
        $data = $request->input('data', []);

        if ($event === 'charge.success' && isset($data['reference'])) {
            $reference = $data['reference'];
            $result = $paystack->verify($reference);

            if ($result['status'] && $result['paid']) {
                $reservation = Reservation::where('payment_reference', $reference)->first()
                    ?? $this->resolveReservationFromReference($reference);

                if ($reservation) {
                    $reservation->recordPayment($result['amount'], 'paystack', $reference);
                }
            }
        }

        return response()->json(['status' => true]);
    }

    /**
     * Try to find a reservation from a Paystack reference. Our references embed
     * the booking id as BR-<id>-..., so fall back to that when needed.
     */
    protected function resolveReservationFromReference(string $reference): ?Reservation
    {
        if (preg_match('/BR-(\d{5})/', $reference, $m)) {
            return Reservation::find((int) $m[1]);
        }

        return null;
    }
}
