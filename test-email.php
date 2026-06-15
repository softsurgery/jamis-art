<?php
session_start();
require_once __DIR__ . "/controllers/EmailController.php";

$message = '';
$status = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $to = $_POST['email'] ?? '';
    $subject = $_POST['subject'] ?? 'Test Email from Jamis Art';
    
    if (filter_var($to, FILTER_VALIDATE_EMAIL)) {
        $emailController = new EmailController();
        // Enable debug so we can see what went wrong in the PHP error logs if it fails
        $emailController->setDebug(true); 
        
        $success = $emailController->sendTemplateEmail(
            $to,
            $subject,
            'example.php', // uses views/emails/example.php
            [
                'name' => 'Tester',
                'message' => 'This is a test email sent from the Jamis Art testing page to verify your SMTP configuration is working correctly.'
            ]
        );
        
        if ($success) {
            $status = 'success';
            $message = "Test email successfully sent to $to!";
        } else {
            $status = 'error';
            $message = "Failed to send email. Please check your PHP error logs for SMTP debug details.";
        }
    } else {
        $status = 'error';
        $message = "Please enter a valid email address.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test SMTP Configuration</title>
    <!-- Use Tailwind from CDN for this standalone test page -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #111; }
        .title-font { font-family: 'Anton', sans-serif; letter-spacing: 1px; }
    </style>
</head>
<body class="text-white min-h-screen flex flex-col items-center justify-center p-6">
    <div class="max-w-md w-full bg-white/5 backdrop-blur-md p-8 rounded-2xl border border-white/10 shadow-2xl">
        <h1 class="text-3xl title-font mb-6 text-center text-red-500">TEST SMTP EMAIL</h1>
        
        <p class="text-gray-400 text-sm mb-6 text-center">
            This tool sends a test message using the settings defined in your <code>.env</code> file.
        </p>

        <?php if ($message): ?>
            <div class="mb-6 p-4 rounded-lg <?= $status === 'success' ? 'bg-green-500/20 text-green-300 border border-green-500/50' : 'bg-red-500/20 text-red-300 border border-red-500/50' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" class="space-y-5">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Recipient Email Address</label>
                <input type="email" name="email" required 
                    class="w-full bg-black/50 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-red-500 transition"
                    placeholder="you@example.com">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Email Subject</label>
                <input type="text" name="subject" value="Jamis Art SMTP Test" required 
                    class="w-full bg-black/50 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-red-500 transition">
            </div>

            <button type="submit" 
                class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 mt-4">
                Send Test Email
            </button>
        </form>
        
        <div class="mt-6 text-center">
            <a href="index.php" class="text-gray-500 hover:text-white text-sm transition">&larr; Return to Home</a>
        </div>
    </div>
</body>
</html>
