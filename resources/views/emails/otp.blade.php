<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        h1 {
            color: #333;
            margin: 0;
        }
        .otp-section {
            background-color: #f9f9f9;
            border: 2px dashed #007bff;
            padding: 30px;
            text-align: center;
            margin: 30px 0;
            border-radius: 8px;
        }
        .otp-code {
            font-size: 36px;
            font-weight: bold;
            letter-spacing: 8px;
            color: #007bff;
            font-family: 'Courier New', monospace;
        }
        .info {
            background-color: #e7f3ff;
            border-left: 4px solid #007bff;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #888;
        }
        .button {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 4px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>
                {{ $type === 'password_reset'
                    ? 'Password Reset'
                    : 'Email Verification' }}
            </h1>
        </div>

        <p>Hi <strong>{{ $userName }}</strong>,</p>

       @if ($type === 'password_reset')
            <p>We received a request to reset the password for your {{ config('app.name') }} account. Please use the OTP code below to continue:</p>
        @else
            <p>Thank you for registering with {{ config('app.name') }}. To complete your registration, please verify your email address using the OTP code below:</p>
        @endif

       <div class="otp-section">
            <p style="margin: 0 0 10px 0; color: #666;">Your OTP Code:</p>
            <div class="otp-code">{{ $otp }}</div>
        </div>

        <div class="info">
            <strong>Important:</strong> This code expires in <strong>10 minutes</strong>. Please enter it in the verification form as soon as possible.
        </div>

       @if ($type === 'password_reset')
            <p style="color: #666;">If you didn't request a password reset, please ignore this email. Your password will not be changed unless you complete the reset process.</p>
        @else
            <p style="color: #666;">If you didn't request this email, please ignore it. Your account will not be created until you verify your email.</p>
        @endif
        
        <hr style="border: none; border-top: 1px solid #ddd; margin: 30px 0;">

        <div class="footer">
            <p>This is an automated email. Please don't reply to this message.</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
