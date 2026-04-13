<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Announcement</title>
</head>
<body style="margin:0; padding:0; background:#f1f5f9; font-family: Arial, Helvetica, sans-serif; color:#334155;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9; padding:24px 0;">
    <tr>
        <td align="center">
            <table role="presentation" width="620" cellpadding="0" cellspacing="0" style="max-width:620px; width:100%;">
                <tr>
                    <td align="center" style="padding:8px 16px 16px;">
                        <img src="{{ rtrim($appUrl, '/') }}/assets/images/LOGO%20(2).png" alt="E-Barangay Health" style="display:block; max-height:72px; width:auto;">
                    </td>
                </tr>
                <tr>
                    <td style="background:#ffffff; border-radius:12px; padding:28px 30px; border:1px solid #e2e8f0;">
                        <p style="margin:0 0 16px; font-size:32px; line-height:1;">Hello {{ $recipientName }},</p>
                        <p style="margin:0 0 18px; font-size:18px; line-height:1.6;">A new announcement has been posted for all patients.</p>

                        <p style="margin:0 0 10px; font-size:18px; line-height:1.6;"><strong>Title:</strong> {{ $announcement->title }}</p>
                        <p style="margin:0 0 10px; font-size:18px; line-height:1.6;"><strong>Message:</strong></p>
                        <p style="margin:0 0 18px; font-size:18px; line-height:1.65; white-space:pre-line;">{{ $announcement->message }}</p>

                        @if($announcement->expires_at)
                            <p style="margin:0 0 18px; font-size:18px; line-height:1.6;"><strong>Expires:</strong> {{ $announcement->expires_at->format('F d, Y') }}</p>
                        @endif

                        <p style="margin:0 0 18px; font-size:18px; line-height:1.6;">Please log in to your account to view any related details or updates.</p>
                        <p style="margin:0; font-size:18px; line-height:1.6;">Regards, E-Barangay Health Team</p>
                    </td>
                </tr>
                <tr>
                    <td align="center" style="padding:14px 16px 0; color:#94a3b8; font-size:13px;">
                        &copy; {{ date('Y') }} E-Barangay Health. All rights reserved.
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
