<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LoginOtpCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public string $code;

    public function __construct(User $user, string $code)
    {
        $this->user = $user;
        $this->code = $code;
    }

    public function build()
    {
        $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Verification Code</title>
</head>
<body style="font-family: Arial, sans-serif; background: #f8fafc; margin: 0; padding: 0;">
    <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 10px; overflow: hidden;">
        <tr>
            <td style="padding: 24px; text-align: center; background: #0f766e; color: #ffffff;">
                <h1 style="margin: 0; font-size: 24px;">KCC Motorcycle</h1>
                <p style="margin: 8px 0 0; font-size: 16px;">Login verification code</p>
            </td>
        </tr>
        <tr>
            <td style="padding: 24px; color: #0f172a;">
                <p style="margin: 0 0 16px;">Hi {$this->user->name},</p>
                <p style="margin: 0 0 16px;">Use the verification code below to complete your login:</p>
                <p style="margin: 0 0 24px; font-size: 26px; font-weight: bold; letter-spacing: 4px; text-align: center;">{$this->code}</p>
                <p style="margin: 0; color: #475569;">If you did not request this code, please ignore this email.</p>
            </td>
        </tr>
        <tr>
            <td style="padding: 24px; background: #f1f5f9; color: #475569; font-size: 14px;">
                <p style="margin: 0;">KCC Motorcycle Security Team</p>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;

        return $this->subject('Your Login Verification Code')
                    ->html($html);
    }
}
