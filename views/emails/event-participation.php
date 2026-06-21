<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Event Registration Confirmation</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #111; color: #fff; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #222; padding: 30px; border-radius: 8px;">
        <h1 style="color: #ef4444; margin-top: 0;">Registration Confirmed!</h1>
        <p>Hello <?= htmlspecialchars($name) ?>,</p>
        <p>Thank you for registering for the upcoming event on Jamis Art.</p>
        
        <div style="background-color: #333; padding: 15px; border-radius: 6px; border-left: 4px solid #ef4444; margin: 20px 0;">
            <h2 style="margin-top: 0; font-size: 18px; color: #fff;"><?= htmlspecialchars($eventTitle) ?></h2>
            <p style="color: #ccc; margin-bottom: 0;"><strong>Date Posted:</strong> <?= htmlspecialchars(date('M d, Y', strtotime($eventDate))) ?></p>
        </div>
        
        <a href="<?= htmlspecialchars($eventUrl) ?>" style="display: inline-block; background-color: #ef4444; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 6px; font-weight: bold;">View Event Details</a>
        
        <p style="margin-top: 30px; color: #888; font-size: 12px;">
            If you did not register for this event, please ignore this email or contact support.
        </p>
    </div>
</body>
</html>
