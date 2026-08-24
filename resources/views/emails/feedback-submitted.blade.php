<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Guest Feedback</title>
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
                            <h2 style="margin:0 0 16px;font-size:20px;color:#111827;">New Guest Feedback</h2>

                            <p style="margin:0 0 12px;color:#374151;font-size:22px;font-weight:bold;color:#f59e0b;">
                                @for($i = 0; $i < $feedback->rating; $i++)&#9733;@endfor
                                <span style="color:#d1d5db;">@for($i = $feedback->rating; $i < 5; $i++)&#9733;@endfor</span>
                                <span style="font-size:14px;color:#6b7280;font-weight:normal;">({{ $feedback->rating }}/5)</span>
                            </p>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:16px 0;border-top:1px solid #e5e7eb;">
                                <tr>
                                    <td style="padding:8px 0;color:#6b7280;font-size:13px;width:120px;">Guest</td>
                                    <td style="padding:8px 0;color:#111827;font-size:14px;font-weight:bold;">{{ $feedback->name ?: 'Anonymous' }}</td>
                                </tr>
                                @if($feedback->email)
                                    <tr>
                                        <td style="padding:8px 0;color:#6b7280;font-size:13px;">Email</td>
                                        <td style="padding:8px 0;color:#111827;font-size:14px;">{{ $feedback->email }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <td style="padding:8px 0;color:#6b7280;font-size:13px;vertical-align:top;">Feedback</td>
                                    <td style="padding:8px 0;color:#111827;font-size:14px;">{{ nl2br(e($feedback->message)) }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 0;color:#6b7280;font-size:13px;">Received</td>
                                    <td style="padding:8px 0;color:#111827;font-size:14px;">{{ $feedback->created_at?->format('d M Y, H:i') ?? now()->format('d M Y, H:i') }}</td>
                                </tr>
                            </table>

                            <p style="margin:16px 0 0;font-size:13px;color:#6b7280;">
                                Approve or reject this feedback in the
                                <a href="{{ route('filament.admin.resources.feedback.index') }}" style="color:#f59e0b;">admin panel</a>.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#f9fafb;padding:12px 24px;color:#9ca3af;font-size:12px;text-align:center;">
                            This is an automated notification from the Brickspoint Hotel website.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
