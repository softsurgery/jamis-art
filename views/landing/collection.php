<?php
session_start();
require_once __DIR__ . "/../../controllers/AuthController.php";
require_once __DIR__ . '/../../controllers/ArtTypeController.php';

$isLoggedIn = isset($_SESSION['user_id']);

$artTypeId = $_GET['type'] ?? null;
$artTypeController = new ArtTypeController();
$artType = null;
$allArtTypes = $artTypeController->getAll();

if ($artTypeId) {
    foreach ($allArtTypes as $type) {
        if ($type['id'] == $artTypeId) {
            $artType = $type;
            break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JemisArt - Collection</title>
    <link rel="stylesheet" href="../../assets/styles.css" />
    <script src="../../assets/js/tailwind.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
</head>

<body class="bg-gray-900 text-white min-h-screen font-body">
    <nav class="fixed top-0 left-0 w-full z-50 glass border-b border-white/10">
        <?php
        require_once __DIR__ . '/../../components/landing/nav.php';
        echo landingNavLayout('collection', $isLoggedIn, './../..');
        ?>
    </nav>

    <main class="pt-24 pb-16 px-4 md:px-8 max-w-7xl mx-auto">
        <?php if ($artType): ?>
            <header class="mb-12 text-center md:text-left">
                <h1 class="text-4xl md:text-6xl font-bold font-display uppercase tracking-wider mb-4"
                    style="color: <?= htmlspecialchars($artType['colorValue'] ?? '#ef4444') ?>;">
                    <?= htmlspecialchars($artType['label']) ?> Collection
                </h1>
                <p class="text-gray-400 text-lg max-w-3xl">Dive deep into our curated selection of articles, resources, and
                    highlights specific to <?= htmlspecialchars($artType['label']) ?> art.</p>
            </header>

            <section class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Articles List -->
                <div class="lg:col-span-2 space-y-8">
                    <h2 class="text-2xl font-bold border-b border-white/20 pb-4">Featured Articles</h2>

                    <article
                        class="bg-gray-800/50 rounded-xl p-6 hover:bg-gray-800 transition-colors border border-white/5">
                        <span class="text-xs font-semibold text-red-500 uppercase tracking-wider">Highlight</span>
                        <h3 class="text-xl font-bold mt-2 mb-3">The Evolution of <?= htmlspecialchars($artType['label']) ?>
                            Techniques</h3>
                        <p class="text-gray-400 mb-4">Explore how styles have shifted and adapted over the centuries,
                            influenced by cultural movements and technological advancements.</p>
                        <a href="#" class="text-red-400 hover:text-red-300 font-medium inline-flex items-center gap-1">
                            Read Article &rarr;
                        </a>
                    </article>

                    <article
                        class="bg-gray-800/50 rounded-xl p-6 hover:bg-gray-800 transition-colors border border-white/5">
                        <span class="text-xs font-semibold text-blue-500 uppercase tracking-wider">Interview</span>
                        <h3 class="text-xl font-bold mt-2 mb-3">Voices from the <?= htmlspecialchars($artType['label']) ?>
                            Community</h3>
                        <p class="text-gray-400 mb-4">A conversation with contemporary masters about their creative process
                            and vision for the future.</p>
                        <a href="#" class="text-red-400 hover:text-red-300 font-medium inline-flex items-center gap-1">
                            Read Article &rarr;
                        </a>
                    </article>

                    <article
                        class="bg-gray-800/50 rounded-xl p-6 hover:bg-gray-800 transition-colors border border-white/5">
                        <span class="text-xs font-semibold text-green-500 uppercase tracking-wider">Gallery Review</span>
                        <h3 class="text-xl font-bold mt-2 mb-3">Top 10 <?= htmlspecialchars($artType['label']) ?> Pieces of
                            the Year</h3>
                        <p class="text-gray-400 mb-4">A definitive list curated by our experts, showcasing breathtaking new
                            works.</p>
                        <a href="#" class="text-red-400 hover:text-red-300 font-medium inline-flex items-center gap-1">
                            Read Article &rarr;
                        </a>
                    </article>
                </div>

                <!-- Sidebar Resources -->
                <div class="space-y-8">
                    <div class="bg-gray-800/80 rounded-xl p-6 border border-white/10">
                        <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
                            <span class="w-2 h-6 bg-red-600 rounded-full"></span>
                            Useful Resources
                        </h2>
                        <ul class="space-y-4">
                            <li>
                                <a href="#" class="block group">
                                    <h4 class="font-medium group-hover:text-red-400 transition-colors">Beginner's Guide</h4>
                                    <p class="text-sm text-gray-500">Essential reading for newcomers.</p>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="block group">
                                    <h4 class="font-medium group-hover:text-red-400 transition-colors">Digital Archive</h4>
                                    <p class="text-sm text-gray-500">Thousands of high-res images.</p>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="block group">
                                    <h4 class="font-medium group-hover:text-red-400 transition-colors">Community Forums</h4>
                                    <p class="text-sm text-gray-500">Connect with other enthusiasts.</p>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="block group">
                                    <h4 class="font-medium group-hover:text-red-400 transition-colors">Exhibition Calendar
                                    </h4>
                                    <p class="text-sm text-gray-500">Find events near you.</p>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="bg-gradient-to-br from-red-600/20 to-purple-600/20 rounded-xl p-6 border border-red-500/20">
                        <h3 class="font-bold text-lg mb-2">Subscribe for Updates</h3>
                        <p class="text-sm text-gray-400 mb-4">Get the latest articles and collection additions straight to
                            your inbox.</p>
                        <form class="flex flex-col gap-3" onsubmit="event.preventDefault(); alert('Subscribed!');">
                            <input type="email" placeholder="Your email address"
                                class="px-4 py-2 bg-gray-900/50 border border-white/10 rounded-lg focus:outline-none focus:border-red-500 text-sm">
                            <button type="submit"
                                class="px-4 py-2 bg-red-600 hover:bg-red-700 rounded-lg font-medium transition-colors text-sm">Subscribe</button>
                        </form>
                    </div>
                </div>
            </section>

        <?php else: ?>
            <!-- Global Collection View (When no type is specified) -->
            <header class="mb-12 text-center">
                <h1 class="text-4xl md:text-5xl font-bold font-display uppercase tracking-wider mb-4 text-red-500">
                    Global Art Collections
                </h1>
                <p class="text-gray-400 text-lg max-w-2xl mx-auto">Explore highlighted links, featured articles, and useful
                    resources across all art disciplines.</p>
            </header>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($allArtTypes as $type): ?>
                    <a href="?type=<?= urlencode($type['id']) ?>"
                        class="group block bg-gray-800/40 hover:bg-gray-800 rounded-xl overflow-hidden border border-white/5 transition-all hover:border-white/20 hover:-translate-y-1">
                        <div class="h-4 w-full"
                            style="background-color: <?= htmlspecialchars($type['colorValue'] ?? '#ef4444') ?>;"></div>
                        <div class="p-6">
                            <h2 class="text-2xl font-bold mb-2"><?= htmlspecialchars($type['label']) ?> Hub</h2>
                            <p class="text-gray-400 text-sm">Articles, resources, and specific layouts for
                                <?= htmlspecialchars(strtolower($type['label'])) ?>.</p>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
</body>

</html>