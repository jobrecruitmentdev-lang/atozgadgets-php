<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password - AtoZGadgets</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #0d0d0d; color: #f5f5f5; margin: 0; padding: 40px 20px; line-height: 1.6;">
    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 560px; background-color: #141414; border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 16px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.5);">
        <tr>
            <td style="padding: 36px 32px; text-align: center; border-bottom: 1px solid rgba(255, 255, 255, 0.08);">
                <span style="font-size: 22px; font-weight: 700; color: #C9A962; letter-spacing: -0.5px;">AtoZGadgets</span>
            </td>
        </tr>
        <tr>
            <td style="padding: 36px 32px;">
                <h2 style="font-size: 20px; font-weight: 600; color: #ffffff; margin-top: 0; margin-bottom: 16px;">Password Reset Request</h2>
                <p style="color: #a0a0a0; font-size: 15px; margin-bottom: 24px;">
                    Hello {{ $user->first_name ?? 'there' }}, we received a request to reset your password for your AtoZGadgets account. Click the button below to set a new password:
                </p>
                <div style="text-align: center; margin: 32px 0;">
                    <a href="{{ $resetUrl }}" style="background-color: #C9A962; color: #0a0a0a; font-size: 15px; font-weight: 600; text-decoration: none; padding: 14px 32px; border-radius: 8px; display: inline-block; letter-spacing: 0.3px;">Reset My Password</a>
                </div>
                <p style="color: #737373; font-size: 13px; margin-bottom: 8px;">
                    This password reset link will expire in <strong>30 minutes</strong>.
                </p>
                <p style="color: #737373; font-size: 13px; margin-bottom: 0;">
                    If you didn't request a password reset, you can safely ignore this email. Your password will remain unchanged.
                </p>
            </td>
        </tr>
        <tr>
            <td style="padding: 24px 32px; background-color: #0f0f0f; border-top: 1px solid rgba(255, 255, 255, 0.05); text-align: center; font-size: 12px; color: #525252;">
                © 2026 AtoZGadgets · 24/7 Global Customer Support · <a href="https://atozgadgetz.com" style="color: #C9A962; text-decoration: none;">atozgadgetz.com</a>
            </td>
        </tr>
    </table>
</body>
</html>
