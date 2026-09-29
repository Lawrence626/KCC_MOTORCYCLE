<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ResendEmailService
{
    /**
     * Send email via Resend HTTP API (Port 443) with auto test-mode routing and Laravel SMTP fallback.
     */
    public static function send(string $toEmail, string $subject, string $htmlContent): bool
    {
        $resendApiKey = env('RESEND_API_KEY');
        $testFallbackEmail = env('RESEND_TEST_RECIPIENT', 'ilanolawrencebryan@gmail.com');

        // If Resend API Key is configured, use Resend API directly over HTTPS Port 443 (Render-compatible)
        if (!empty($resendApiKey)) {
            try {
                $fromAddress = env('RESEND_FROM_ADDRESS', 'KCC Motorcycle <onboarding@resend.dev>');
                if (str_contains($fromAddress, '@gmail.com') || str_contains($fromAddress, '@yahoo.com')) {
                    $fromAddress = 'KCC Motorcycle <onboarding@resend.dev>';
                }

                $response = Http::withToken($resendApiKey)
                    ->timeout(10)
                    ->post('https://api.resend.com/emails', [
                        'from'    => $fromAddress,
                        'to'      => [$toEmail],
                        'subject' => $subject,
                        'html'    => $htmlContent,
                    ]);

                if ($response->successful()) {
                    Log::info("Email sent via Resend API to {$toEmail}: ID " . ($response->json('id') ?? 'success'));
                    return true;
                }

                // If Resend 403 (Test mode restriction: can only send to registered Resend email)
                $body = $response->body();
                if ($response->status() === 403 && str_contains($body, 'only send testing emails to your own email address')) {
                    Log::info("Resend is in test mode. Forwarding OTP to account owner ({$testFallbackEmail}) for target: {$toEmail}");
                    
                    $fwdSubject = "[For {$toEmail}] " . $subject;
                    $fwdHtml = "<div style='padding:12px;background:#fef3c7;border:1px solid #f59e0b;border-radius:8px;margin-bottom:16px;color:#92400e;font-size:13px;'><strong>Notice:</strong> This verification code was requested for account <code>{$toEmail}</code>.</div>" . $htmlContent;

                    $fwdResponse = Http::withToken($resendApiKey)
                        ->timeout(10)
                        ->post('https://api.resend.com/emails', [
                            'from'    => $fromAddress,
                            'to'      => [$testFallbackEmail],
                            'subject' => $fwdSubject,
                            'html'    => $fwdHtml,
                        ]);

                    if ($fwdResponse->successful()) {
                        Log::info("Forwarded test email via Resend to {$testFallbackEmail} for {$toEmail}");
                        return true;
                    }
                }

                Log::warning("Resend API failed ({$response->status()}): " . $body);
            } catch (\Throwable $e) {
                Log::warning("Resend API exception: " . $e->getMessage());
            }
        }

        // Fallback to Laravel Mail (SMTP)
        try {
            Mail::html($htmlContent, function ($msg) use ($toEmail, $subject) {
                $msg->to($toEmail)->subject($subject);
            });
            Log::info("Email sent via Laravel Mail to {$toEmail}");
            return true;
        } catch (\Throwable $e) {
            Log::warning("Laravel Mail fallback failed for {$toEmail}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send Login OTP code.
     */
    public static function sendLoginOtp(User $user, string $otpCode): bool
    {
        $subject = 'KCC Motorcycle - Login Verification Code';
        $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Verification Code</title>
</head>
<body style="font-family: Arial, sans-serif; background: #0f172a; margin: 0; padding: 30px 10px;">
    <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 540px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
        <tr>
            <td style="padding: 30px; text-align: center; background: #0f172a; color: #ffffff;">
                <h1 style="margin: 0; font-size: 24px; font-weight: bold; color: #6EC1D1;">KCC MOTORCYCLE</h1>
                <p style="margin: 6px 0 0; font-size: 14px; color: #94a3b8;">Two-Factor Authentication</p>
            </td>
        </tr>
        <tr>
            <td style="padding: 32px 28px; color: #1e293b;">
                <p style="margin: 0 0 16px; font-size: 15px;">Hello <strong>{$user->name}</strong>,</p>
                <p style="margin: 0 0 20px; font-size: 14px; color: #475569; line-height: 1.5;">Your one-time security code for accessing your KCC Motorcycle POS & Inventory account is:</p>
                <div style="background: #f8fafc; border: 2px dashed #6EC1D1; border-radius: 12px; padding: 18px; text-align: center; margin: 0 0 24px;">
                    <span style="font-size: 32px; font-weight: 800; letter-spacing: 8px; color: #0f172a; font-family: monospace;">{$otpCode}</span>
                </div>
                <p style="margin: 0 0 8px; font-size: 13px; color: #64748b;">⏳ This verification code will expire in <strong>10 minutes</strong>.</p>
                <p style="margin: 0; font-size: 13px; color: #94a3b8;">If you did not request this login attempt, please secure your account immediately.</p>
            </td>
        </tr>
        <tr>
            <td style="padding: 20px; background: #f1f5f9; color: #64748b; font-size: 12px; text-align: center; border-top: 1px solid #e2e8f0;">
                <p style="margin: 0;">© 2026 KCC Motorcycle Trading & Repair Shop. All rights reserved.</p>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;

        return self::send($user->email, $subject, $html);
    }

    /**
     * Send Forgot Password OTP code.
     */
    public static function sendResetOtp(string $email, string $otpCode): bool
    {
        $subject = 'KCC Motorcycle - Password Reset Code';
        $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset Code</title>
</head>
<body style="font-family: Arial, sans-serif; background: #0f172a; margin: 0; padding: 30px 10px;">
    <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 540px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
        <tr>
            <td style="padding: 30px; text-align: center; background: #0f172a; color: #ffffff;">
                <h1 style="margin: 0; font-size: 24px; font-weight: bold; color: #6EC1D1;">KCC MOTORCYCLE</h1>
                <p style="margin: 6px 0 0; font-size: 14px; color: #94a3b8;">Password Reset Request</p>
            </td>
        </tr>
        <tr>
            <td style="padding: 32px 28px; color: #1e293b;">
                <p style="margin: 0 0 16px; font-size: 15px;">Hello,</p>
                <p style="margin: 0 0 20px; font-size: 14px; color: #475569; line-height: 1.5;">We received a request to reset your password. Use the following code to continue:</p>
                <div style="background: #f8fafc; border: 2px dashed #6EC1D1; border-radius: 12px; padding: 18px; text-align: center; margin: 0 0 24px;">
                    <span style="font-size: 32px; font-weight: 800; letter-spacing: 8px; color: #0f172a; font-family: monospace;">{$otpCode}</span>
                </div>
                <p style="margin: 0 0 8px; font-size: 13px; color: #64748b;">⏳ This reset code will expire in <strong>15 minutes</strong>.</p>
                <p style="margin: 0; font-size: 13px; color: #94a3b8;">If you did not request a password reset, you can safely ignore this email.</p>
            </td>
        </tr>
        <tr>
            <td style="padding: 20px; background: #f1f5f9; color: #64748b; font-size: 12px; text-align: center; border-top: 1px solid #e2e8f0;">
                <p style="margin: 0;">© 2026 KCC Motorcycle Trading & Repair Shop. All rights reserved.</p>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;

        return self::send($email, $subject, $html);
    }
}
