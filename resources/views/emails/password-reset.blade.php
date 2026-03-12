<p>Hello {{ $user->full_name }},</p>

<p>You requested a password reset. Click the link below to reset your password:</p>

<p><a href="{{ $resetLink }}" class="text-blue-600">{{ $resetLink }}</a></p>

<p>This link is valid for 60 minutes.</p>

<p>If you did not request this, please ignore this email.</p>

<p>Regards,<br>E-Barangay Health Team</p>
