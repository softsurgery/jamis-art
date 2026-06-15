<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($subject ?? 'Jamis Art Notification') ?></title>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }

        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            max-width: 600px;
            margin: 0 auto;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        h1 {
            color: #333333;
            font-size: 24px;
        }

        p {
            color: #555555;
            line-height: 1.6;
        }

        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #999999;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Hello <?= htmlspecialchars($name ?? 'User') ?>,</h1>
        <p><?= htmlspecialchars($message ?? 'You have a new notification.') ?></p>

        <div class="footer">
            &copy; <?= date('Y') ?> Jamis Art. All rights reserved.
        </div>
    </div>
</body>

</html>