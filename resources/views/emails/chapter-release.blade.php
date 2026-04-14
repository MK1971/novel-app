<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $headline }}</title>
</head>
<body style="margin:0;padding:0;background:#fff9f0;font-family:Arial,sans-serif;color:#1f2937;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#fff9f0;padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:600px;background:#ffffff;border:1px solid #fde68a;border-radius:14px;overflow:hidden;">
                    <tr>
                        <td style="padding:24px 24px 8px;">
                            <p style="margin:0 0 10px;font-size:12px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:#92400e;">Chapter update</p>
                            <h1 style="margin:0 0 8px;font-size:24px;line-height:1.25;color:#92400e;">{{ $headline }}</h1>
                            <p style="margin:0 0 20px;font-size:15px;line-height:1.6;">{{ $summary }}</p>
                            <p style="margin:0;">
                                <a href="{{ $actionUrl }}" style="display:inline-block;background:#d97706;color:#ffffff;text-decoration:none;font-weight:700;padding:10px 16px;border-radius:10px;">{{ $actionLabel }}</a>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:18px 24px 24px;color:#6b7280;font-size:12px;line-height:1.5;">
                            You are receiving this because you joined WhatsMyBookName chapter updates.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

