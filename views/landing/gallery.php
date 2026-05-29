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
    <script src="../../assets/js/tailwind.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
</head>

<body class="text-white">
    <nav class="fixed top-0 left-0 w-full z-50 glass">
        <?php
        require_once __DIR__ . '/../../components/landing/nav.php';
        echo landingNavLayout('gallery', $isLoggedIn, '../../');
        ?>
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
                        <div class="flex flex-col gap-2">
                            <a href="#"
                                class="px-4 py-2 md:px-6 md:py-2 bg-red-600 hover:bg-red-700 text-white rounded-full text-xs md:text-sm font-medium transition-colors shadow-lg shadow-red-600/30 whitespace-nowrap">
                                View Collection
                            </a>
                            <a href="./map.php?type=<?= urlencode($artType['id']) ?>"
                                class="px-4 py-2 md:px-6 md:py-2 bg-red-600 hover:bg-red-700 text-white rounded-full text-xs md:text-sm font-medium transition-colors shadow-lg shadow-red-600/30 whitespace-nowrap">
                                Discover Locations
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

    </section>
</body>

</html>