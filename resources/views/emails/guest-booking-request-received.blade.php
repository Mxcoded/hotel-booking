<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Booking Request Received</title>
</head>
<body style="margin:0;padding:0;background:#f4f4f5;font-family:Arial,Helvetica,sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f5;padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.1);">
                    <tr>
                        <td style="background:#111827;padding:16px 24px;color:#ffffff;font-size:18px;font-weight:bold;">
                            Brickspoint Hotel
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:24px;">
                            <h2 style="margin:0 0 12px;font-size:20px;color:#111827;">Hello {{ $reservation->guest_name }},</h2>
                            <p style="margin:0 0 16px;color:#374151;font-size:14px;">
                                Thank you for choosing Brickspoint Hotel. We have received your booking request
                                and will confirm it shortly. Here is a summary of your request:
                            </p>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:16px 0;border-top:1px solid #e5e7eb;">
                                <tr>
                                    <td style="padding:8px 0;color:#6b7280;font-size:13px;width:140px;">Reference</td>
                                    <td style="padding:8px 0;color:#111827;font-size:14px;font-weight:bold;">BR-{{ str_pad((string) $reservation->id, 5, '0', STR_PAD_LEFT) }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 0;color:#6b7280;font-size:13px;">Room type</td>
                                    <td style="padding:8px 0;color:#111827;font-size:14px;">{{ $reservation->roomType?->name }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 0;color:#6b7280;font-size:13px;">Check-in</td>
                                    <td style="padding:8px 0;color:#111827;font-size:14px;">{{ $reservation->check_in->format('D, d M Y') }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 0;color:#6b7280;font-size:13px;">Check-out</td>
                                    <td style="padding:8px 0;color:#111827;font-size:14px;">{{ $reservation->check_out->format('D, d M Y') }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 0;color:#6b7280;font-size:13px;">Guests</td>
                                    <td style="padding:8px 0;color:#111827;font-size:14px;">{{ $reservation->guests }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 0;color:#6b7280;font-size:13px;">Estimated total</td>
                                    <td style="padding:8px 0;color:#111827;font-size:14px;font-weight:bold;">₦{{ number_format((float) $reservation->quoted_total, 2) }}</td>
                                </tr>
                            </table>

                            <p style="margin:16px 0 0;font-size:13px;color:#6b7280;">
                                Our team will reach out to confirm and finalise your stay. If you have any questions,
                                call us on
                                <a href="tel:+2348099999620" style="color:#f59e0b;">+234 809 999 9620</a>.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#f9fafb;padding:12px 24px;color:#9ca3af;font-size:12px;text-align:center;">
                            11 Adzope Crescent, off Kumasi Crescent, Wuse, Abuja
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
