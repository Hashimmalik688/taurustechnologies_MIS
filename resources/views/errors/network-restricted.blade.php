<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Denied | Taurus CRM</title>
    <link rel="shortcut icon" href="{{ URL::asset('images/favicon.ico') }}">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #1a1d21;
            color: #e4e6eb;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .container {
            text-align: center;
            max-width: 500px;
            padding: 40px;
        }
        .shield {
            width: 80px;
            height: 80px;
            margin: 0 auto 24px;
            background: var(--bs-status-absent);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
        }
        h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 12px;
            color: #fff;
        }
        .message {
            font-size: 16px;
            color: var(--bs-surface-muted);
            line-height: 1.6;
            margin-bottom: 24px;
        }
        .ip-badge {
            display: inline-block;
            background: #2d3748;
            border: 1px solid #4a5568;
            border-radius: 6px;
            padding: 8px 16px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            color: #f56565;
            margin-bottom: 24px;
        }
        .help-text {
            font-size: 13px;
            color: var(--bs-surface-500);
            line-height: 1.5;
        }
        .btn-login {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 24px;
            background: #4a5568;
            color: #e4e6eb;
            text-decoration: none;
            border-radius: 6px;
            font-size: 14px;
            transition: background 0.2s;
        }
        .btn-login:hover { background: #5a6578; }
    </style>
</head>
<body>
    <div class="container">
        <div class="shield">&#128274;</div>
        <h1>Access Denied</h1>
        <p class="message">
            Your network is not authorized to access Taurus CRM.
            This system can only be accessed from the office network.
        </p>
        <div class="ip-badge">Your IP: {{ $ip }}</div>
        <p class="help-text">
            If you believe this is an error, contact your system administrator
            to add your network to the allowed list in System Settings.
        </p>
        <a href="/login" class="btn-login">Back to Login</a>
    </div>
</body>
</html>
