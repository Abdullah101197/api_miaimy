<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>OTP Verification</title>
</head>
<body>
    <h2>OTP Verification</h2>
    <p>Hello {{ $user->name }},</p>
    <p>Thank you for registering</p>
   {{ $otp }}
    <p>If you didn't request this OTP, you can ignore this email.</p>
</body>
</html>
