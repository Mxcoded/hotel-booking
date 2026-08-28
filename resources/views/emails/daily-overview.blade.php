<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daily Overview</title>
</head>
<body style="margin:0;padding:0;background:#f4f4f5;font-family:Arial,Helvetica,sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f5;padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.1);">
                    <tr>
                        <td style="background:#111827;padding:16px 24px;color:#ffffff;font-size:18px;font-weight:bold;">
                            Brickspoint Hotel — Daily Overview
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:24px;">
                            <h2 style="margin:0 0 16px;font-size:20px;color:#111827;">{{ now()->format('D, d M Y') }}</h2>

                            <h3 style="margin:0 0 8px;color:#111827;font-size:15px;">Expected arrivals ({{ $arrivals->count() }})</h3>
                            @if($arrivals->isEmpty())
                                <p style="margin:0 0 20px;color:#6b7280;font-size:13px;">No arrivals expected today.</p>
                            @else
                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 24px;border-top:1px solid #e5e7eb;">
                                    @foreach($arrivals as $r)
                                    <tr>
                                        <td style="padding:8px 0;color:#6b7280;font-size:13px;">{{ $r->guest_name }}</td>
                                        <td style="padding:8px 0;color:#111827;font-size:14px;">{{ $r->roomType?->name }}</td>
                                        <td style="padding:8px 0;color:#111827;font-size:14px;">BR-{{ str_pad((string) $r->id, 5, '0', STR_PAD_LEFT) }}</td>
                                    </tr>
                                    @endforeach
                                </table>
                            @endif

                            <h3 style="margin:0 0 8px;color:#111827;font-size:15px;">Expected departures ({{ $departures->count() }})</h3>
                            @if($departures->isEmpty())
                                <p style="margin:0;color:#6b7280;font-size:13px;">No departures expected today.</p>
                            @else
                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0;border-top:1px solid #e5e7eb;">
                                    @foreach($departures as $r)
                                    <tr>
                                        <td style="padding:8px 0;color:#6b7280;font-size:13px;">{{ $r->guest_name }}</td>
                                        <td style="padding:8px 0;color:#111827;font-size:14px;">{{ $r->roomType?->name }}</td>
                                        <td style="padding:8px 0;color:#111827;font-size:14px;">BR-{{ str_pad((string) $r->id, 5, '0', STR_PAD_LEFT) }}</td>
                                    </tr>
                                    @endforeach
                                </table>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#f9fafb;padding:12px 24px;color:#9ca3af;font-size:12px;text-align:center;">
                            Automated daily briefing from Brickspoint Hotel.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
