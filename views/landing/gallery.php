<?php
session_start();
// require_once "connect.php";
require_once __DIR__ . "/../../controllers/AuthController.php";
require_once __DIR__ . '/../../controllers/ArtTypeController.php';
require_once __DIR__ . '/../../lib/random.php';


$isLoggedIn = isset($_SESSION['user_id']);

$artTypeController = new ArtTypeController();
$artTypes = $artTypeController->getAll();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JemisArt - Gallery</title>
    <link rel="stylesheet" href="../../assets/styles.css" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
</head>

<body class="text-white">
    <nav class="fixed top-0 left-0 w-full z-50 glass">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">

            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-red-600"></div>
                <h1 class="text-2xl title-font tracking-wider">
                    ART<span class="gradient-text">VERSE</span>
                </h1>
            </div>

            <div class="hidden md:flex items-center gap-10 text-sm uppercase tracking-widest">
                <a href="../../" class="hover:text-red-500 transition">Home</a>
                <a href="views/landing/gallery.php" class="hover:text-red-500 transition">Gallery</a>
            </div>

            <?php if ($isLoggedIn): ?>
                <a href="views/auth/sign-out.php"
                    class="bg-red-600 hover:bg-red-700 px-5 py-2 rounded-full font-semibold transition">
                    Sign Out
                </a>
            <?php else: ?>
                <button class="bg-red-600 hover:bg-red-700 px-5 py-2 rounded-full font-semibold transition"
                    onclick="window.location.href='views/auth/sign-in.php'">
                    Join Now
                </button>
            <?php endif; ?>

        </div>
    </nav>
    <!-- HERO SECTION -->
    <section class="h-screen w-full flex flex-col md:flex-row overflow-hidden">

        <?php foreach ($artTypes as $artType): ?>
            <div class="group flex-1 [perspective:1000px] cursor-pointer relative h-full w-full">
                <div
                    class="absolute inset-0 h-full w-full transition-transform duration-700 [transform-style:preserve-3d] group-hover:[transform:rotateY(180deg)]">
                    <!-- Front Face -->
                    <div class="absolute inset-0 h-full w-full [backface-visibility:hidden] flex flex-col items-center justify-center border-b md:border-b-0 md:border-r border-white/10"
                        style="background-color: <?= $artType['colorValue'] ?? randomColor() ?>;">
                        <h2
                            class="text-2xl md:text-3xl lg:text-4xl font-bold text-center tracking-wide drop-shadow-lg px-2">
                            <?= htmlspecialchars($artType['label']) ?>
                        </h2>
                    </div>
                    <!-- Back Face -->
                    <div
                        class="absolute inset-0 h-full w-full bg-gray-900/95 [backface-visibility:hidden] [transform:rotateY(180deg)] flex flex-col items-center justify-center p-4 text-center border-b md:border-b-0 md:border-r border-red-500/30 backdrop-blur-md">
                        <h3
                            class="text-lg md:text-xl font-bold mb-2 md:mb-4 text-red-500 uppercase tracking-widest leading-tight">
                            <?= htmlspecialchars($artType['label']) ?>
                        </h3>
                        <p class="text-gray-400 text-xs md:text-sm mb-4 md:mb-6 hidden sm:block">Experience
                            <?= htmlspecialchars(strtolower($artType['label'])) ?> art.
                        </p>
                        <a href="#"
                            class="px-4 py-2 md:px-6 md:py-2 bg-red-600 hover:bg-red-700 text-white rounded-full text-xs md:text-sm font-medium transition-colors shadow-lg shadow-red-600/30 whitespace-nowrap">
                            View Collection
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

    </section>
</body>

</html>