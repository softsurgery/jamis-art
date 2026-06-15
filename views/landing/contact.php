<?php
session_start();
require_once __DIR__ . "/../../controllers/AuthController.php";
require_once __DIR__ . '/../../controllers/ArtTypeController.php';
require_once __DIR__ . '/../../controllers/SupportMessageController.php';
require_once __DIR__ . '/../../controllers/UserController.php';

$isLoggedIn = isset($_SESSION['user_id']);
$artTypeController = new ArtTypeController();
$artTypes = $artTypeController->getAll();

$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    $email = '';
    $artTypeId = null;
    $userId = null;

    if ($isLoggedIn) {
        $userId = $_SESSION['user_id'];
        $userController = new UserController();
        $user = $userController->getById($userId);
        if ($user) {
            $email = $user->getEmail();
        }
    } else {
        $email = trim($_POST['email'] ?? '');
        $artTypeId = $_POST['artTypeId'] ?? null;
        if (empty($artTypeId)) {
            $artTypeId = null;
        }
    }

    if (empty($subject) || empty($message) || (!$isLoggedIn && empty($email))) {
        $errorMessage = "Please fill all required fields.";
    } else {
        $supportMessage = new SupportMessage(null, $subject, $message, $userId, $email, $artTypeId);
        $smController = new SupportMessageController();
        if ($smController->save($supportMessage)) {
            $successMessage = "Your message has been sent successfully. We will get back to you soon!";
        } else {
            $errorMessage = "Failed to send the message. Please try again later.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JemisArt - Contact Us</title>
    <link rel="stylesheet" href="../../assets/styles.css" />
    <script src="../../assets/js/tailwind.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
</head>

<body class="bg-gray-900 text-white min-h-screen flex flex-col font-['Poppins']">
    <nav class="fixed top-0 left-0 w-full z-50 glass">
        <?php
        require_once __DIR__ . '/../../components/landing/nav.php';
        echo landingNavLayout('contact-us', $isLoggedIn, './../..');
        ?>
    </nav>

    <main class="flex-grow pt-32 pb-16 px-6 max-w-4xl mx-auto w-full flex flex-col items-center">

        <h1
            class="text-4xl md:text-5xl font-['Anton'] text-center tracking-wide drop-shadow-lg mb-4 text-white uppercase">
            Contact Us</h1>
        <p class="text-gray-400 text-center mb-10 text-sm md:text-base max-w-2xl">
            We'd love to hear from you! Whether you have a question about art, need support, or just want to connect,
            feel free to drop us a message.
        </p>

        <div
            class="bg-white/5 border border-white/10 p-8 rounded-2xl w-full backdrop-blur-md shadow-xl shadow-black/50">

            <?php if (!empty($successMessage)): ?>
                <div
                    class="mb-6 p-4 rounded bg-green-500/20 border border-green-500/50 text-green-400 text-center text-sm font-medium">
                    <?= htmlspecialchars($successMessage) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($errorMessage)): ?>
                <div
                    class="mb-6 p-4 rounded bg-red-500/20 border border-red-500/50 text-red-400 text-center text-sm font-medium">
                    <?= htmlspecialchars($errorMessage) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="contact.php" class="flex flex-col gap-6">

                <?php if (!$isLoggedIn): ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="flex flex-col gap-2">
                            <label for="email" class="text-sm font-semibold text-gray-300">Email Address <span
                                    class="text-red-500">*</span></label>
                            <input type="email" id="email" name="email" required
                                class="bg-black/50 border border-gray-700 rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors text-white"
                                placeholder="john@example.com">
                        </div>
                        <div class="flex flex-col gap-2">
                            <label for="artTypeId" class="text-sm font-semibold text-gray-300">Area of Interest</label>
                            <select id="artTypeId" name="artTypeId"
                                class="bg-black/50 border border-gray-700 rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors text-white">
                                <option value="">General Inquiry</option>
                                <?php foreach ($artTypes as $type): ?>
                                    <option value="<?= htmlspecialchars($type['id']) ?>"><?= htmlspecialchars($type['label']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="flex flex-col gap-2">
                    <label for="subject" class="text-sm font-semibold text-gray-300">Subject <span
                            class="text-red-500">*</span></label>
                    <input type="text" id="subject" name="subject" required
                        class="bg-black/50 border border-gray-700 rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors text-white"
                        placeholder="How can we help you?">
                </div>

                <div class="flex flex-col gap-2">
                    <label for="message" class="text-sm font-semibold text-gray-300">Message <span
                            class="text-red-500">*</span></label>
                    <textarea id="message" name="message" rows="5" required
                        class="bg-black/50 border border-gray-700 rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors text-white resize-none"
                        placeholder="Write your message here..."></textarea>
                </div>

                <button type="submit"
                    class="mt-2 w-full md:w-auto md:self-end px-8 py-3 bg-red-600 hover:bg-red-700 text-white rounded-full font-semibold transition-colors shadow-lg shadow-red-600/30">
                    Send Message
                </button>
            </form>
        </div>
    </main>

    <footer class="mt-auto border-t border-white/10 py-6 text-center text-xs text-gray-500">
        &copy; <?= date('Y') ?> JemisArt. All rights reserved.
    </footer>

</body>

</html>