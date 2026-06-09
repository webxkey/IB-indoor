<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
            background-color: #f9fafb;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .content {
            padding: 30px 20px;
        }
        .greeting {
            font-size: 16px;
            color: #1f2937;
            margin-bottom: 20px;
        }
        .otp-section {
            background-color: #f3f4f6;
            border: 2px dashed #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 30px 0;
        }
        .otp-code {
            font-size: 36px;
            font-weight: 700;
            color: #667eea;
            letter-spacing: 4px;
            font-family: 'Courier New', monospace;
            margin: 10px 0;
        }
        .otp-message {
            font-size: 14px;
            color: #6b7280;
            margin-top: 10px;
        }
        .validity {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 12px 16px;
            margin: 20px 0;
            border-radius: 4px;
            font-size: 14px;
            color: #92400e;
        }
        .footer {
            background-color: #f9fafb;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
        }
        .warning {
            background-color: #fee2e2;
            border-left: 4px solid #ef4444;
            padding: 12px 16px;
            margin: 20px 0;
            border-radius: 4px;
            font-size: 13px;
            color: #991b1b;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏆 SPORTYNIX HUB</h1>
            <p style="margin: 5px 0 0; font-size: 14px;">Admin Dashboard</p>
        </div>

        <div class="content">
            <div class="greeting">
                Hi <strong>{{ $userFirstName }}</strong>,
            </div>

            <p style="color: #374151; font-size: 15px; line-height: 1.6;">
                Your <strong>{{ $otpType }}</strong> code is ready. Use this code to verify your account:
            </p>

            <div class="otp-section">
                <div class="otp-message">Your One-Time Password</div>
                <div class="otp-code">{{ $otp }}</div>
                <div class="otp-message">Never share this code with anyone</div>
            </div>

            <div class="validity">
                ⏰ <strong>This code expires in 15 minutes</strong>
            </div>

            <div class="warning">
                <strong>Security Alert:</strong> If you didn't request this code, please ignore this email. Your account is safe.
            </div>

            <p style="color: #6b7280; font-size: 14px; margin-top: 20px;">
                Need help? Contact our support team at <a href="mailto:support@sportynixhub.com" style="color: #667eea;">support@sportynixhub.com</a>
            </p>
        </div>

        <div class="footer">
            <p style="margin: 0;">© 2026 SPORTYNIX HUB. All rights reserved.</p>
            <p style="margin: 5px 0 0;">This is an automated message, please do not reply.</p>
        </div>
    </div>
</body>
</html>
