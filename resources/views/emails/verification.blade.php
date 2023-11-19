<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Email Verification</title>
</head>
<body>
    <h2>Email Verification</h2>
    <p>Hello {{ $user->name }},</p>
    <p>Thank you for registering. Please verify your Account:</p>
   {{ $otp }}
    <p>If you didn't request this verification, you can ignore this email.</p>
</body>
</html>
