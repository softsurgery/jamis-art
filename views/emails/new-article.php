<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>New Article on Jamis Art</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #111; color: #fff; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #222; padding: 30px; border-radius: 8px;">
        <h1 style="color: #ef4444; margin-top: 0;">New Article Published!</h1>
        <p>Hello <?= htmlspecialchars($name) ?>,</p>
        <p>A new article has just been published in your preferred art category: <strong><?= htmlspecialchars($artTypeLabel) ?></strong>!</p>
        
        <div style="background-color: #333; padding: 15px; border-radius: 6px; border-left: 4px solid #ef4444; margin: 20px 0;">
            <h2 style="margin-top: 0; font-size: 18px; color: #fff;"><?= htmlspecialchars($articleTitle) ?></h2>
            <p style="color: #ccc; margin-bottom: 0;"><?= htmlspecialchars($articleDescription) ?></p>
        </div>
        
        <a href="<?= htmlspecialchars($articleUrl) ?>" style="display: inline-block; background-color: #ef4444; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 6px; font-weight: bold;">Read Article</a>
        
        <p style="margin-top: 30px; color: #888; font-size: 12px;">
            You are receiving this email because you registered on Jamis Art with an interest in <?= htmlspecialchars($artTypeLabel) ?>.
        </p>
    </div>
</body>
</html>
