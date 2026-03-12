<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Password Reset</title>
</head>
<body>
    <p>Hello,</p>

    <p>You requested to reset your password for E-Barangay Health.</p>

    <p>
        Click the link below to reset your password. 
        This link will expire in 60 minutes.
    </p>

    <p>
        <a href="{{ url('password/reset/' . $token . '?email=' . urlencode($email)) }}">
            Reset Password
        </a>
    </p>

    <p>If you did not request this password reset, you can safely ignore this email.</p>

    <p>Regards,<br>E-Barangay Health Team</p>
</body>
</html>
