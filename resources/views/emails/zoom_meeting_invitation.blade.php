<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Zoom Meeting Invitation</title>
</head>
<style>
    .main-container {
        font-family: Arial, sans-serif;
        max-width: 600px;
        margin: 0 auto;
        padding: 20px;
        background-color: #f5f5f5;
        border-radius: 5px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .header {
        text-align: center;
        color: #007bff;
        font-size: 24px;
        margin-bottom: 30px;
    }

    .meeting-details {
        background-color: #ffffff;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .meeting-details p {
        margin: 0;
        font-size: 16px;
        color: #333333;
    }

    .meeting-details h3 {
        margin-top: 0;
        color: #007bff;
    }

    .thank-you {
        margin-top: 30px;
        text-align: center;
        font-size: 16px;
        color: #333333;
    }

    .zoom-team {
        text-align: center;
        margin-top: 10px;
        font-size: 14px;
        color: #666666;
    }
</style>

<body>

    <div class="main-container">
        <div class="header">
            Miamy Meeting Invitation
        </div>

        <div class="meeting-details">
            <h3>Meeting Details:</h3>
            <p><strong>Meeting URL:</strong> <a href="{{ $joinUrl }}" target="_blank">{{ $joinUrl }}</a></p>
            <p><strong>Meeting Password:</strong> {{ $meetingPassword }}</p>
        </div>

        <div class="thank-you">
            Thank you for using Zoom for your meeting. If you have any questions or need assistance, please don't
            hesitate to contact us.
        </div>

        <div class="zoom-team">
            Regards,
            <br>
            The Maimy Team
        </div>
    </div>

</body>

</html>
