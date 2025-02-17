<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .code {
            font-size: 20px;
            font-weight: bold;
            color: blue;
        }
        #resendBtn {
            background-color: #007bff;
            color: white;
            padding: 10px;
            border: none;
            cursor: pointer;
            margin-top: 10px;
        }
        #resendBtn:disabled {
            background-color: gray;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <h2>Email Verification</h2>
    <p>Hi {{ $user->name }},</p>
    <p>Thank you for registering. Your verification code is:</p>
    <p class="code" id="code">{{ $verificationCode }}</p>
    <p>This code will expire at <strong>{{ $user->verification_expires_at->format('h:i A') }}</strong> ({{ $user->verification_expires_at->timezoneName }}).</p>
    <p>If you did not request this, please ignore this email.</p>

    {{--
    <p id="message" style="color: green;"><a href="{{route("resend-verification")}}">Resend verification code</a></p>


 --}}

</body>
</html>
