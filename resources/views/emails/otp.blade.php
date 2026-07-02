<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset OTP</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 20px; background-color: #f4f4f4;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
        <div style="background: linear-gradient(135deg, #14b8a6, #0d9488); padding: 30px; text-align: center;">
            <h1 style="color: #ffffff; margin: 0; font-size: 28px;">KCC Motorcycle</h1>
            <p style="color: #ffffff; margin: 10px 0 0 0; font-size: 16px;">Password Reset Request</p>
        </div>
        <div style="padding: 30px;">
            <p style="font-size: 16px; margin-bottom: 20px;">Hello,</p>
            <p style="font-size: 16px; margin-bottom: 20px;">We received a request to reset your password for your KCC Motorcycle account. Use the One-Time Password (OTP) below to proceed with the password reset:</p>
            
            <div style="background-color: #f8fafc; border: 2px solid #14b8a6; border-radius: 8px; padding: 20px; text-align: center; margin: 30px 0;">
                <span style="font-size: 36px; font-weight: bold; color: #14b8a6; letter-spacing: 5px;">{{ $otp }}</span>
            </div>
            
            <p style="font-size: 14px; color: #666; margin-bottom: 20px;">This OTP will expire in 15 minutes. If you did not request a password reset, please ignore this email.</p>
            
            <p style="font-size: 16px;">Best regards,<br>KCC Motorcycle Team</p>
        </div>
        <div style="background-color: #f8fafc; padding: 20px; text-align: center; border-top: 1px solid #e5e7eb;">
            <p style="font-size: 12px; color: #9ca3af; margin: 0;">© {{ date('Y') }} KCC Motorcycle. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
