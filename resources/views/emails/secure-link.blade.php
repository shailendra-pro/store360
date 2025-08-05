<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Secure Access Link</title>
</head>
<body>
    <p>Hello {{ $user->name ?? 'User' }},</p>

    <p>You have been granted secure access. Below are your details:</p>

    <p><strong>Login Email:</strong> {{ $email }}</p>
    <p><strong>Secure Link:</strong> <a href="{{ $secureUrl }}">{{ $secureUrl }}</a></p>

    <p>This link expires on: {{ $user->secure_link_expires_at }}</p>

    <br>
    <p>Regards,<br>Store 360 Team</p>
</body>
</html>
