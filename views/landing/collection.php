<?php
session_start();
require_once __DIR__ . "/../../controllers/AuthController.php";
require_once __DIR__ . '/../../controllers/ArtTypeController.php';
require_once __DIR__ . '/../../controllers/ArticleController.php';
require_once __DIR__ . '/collection/article.php';

$isLoggedIn = isset($_SESSION['user_id']);

$artTypeId = $_GET['type'] ?? null;
$artTypeController = new ArtTypeController();
$articleController = new ArticleController();
$artType = null;
$allArtTypes = $artTypeController->getAll();
$articles = [];

if ($artTypeId) {
    foreach ($allArtTypes as $type) {
        if ($type['id'] == $artTypeId) {
            $artType = $type;
            break;
        }
    }
    // Fetch articles for this art type
    $articles = $articleController->getByArtTypeId($artTypeId);
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

    <main class="pt-36 pb-16 px-4 md:px-8 max-w-7xl mx-auto">
        <!-- Breadcrumbs -->
        <nav class="text-sm text-gray-400 mb-8" aria-label="Breadcrumb">
            <ol class="list-none p-0 inline-flex">
                <li class="flex items-center">
                    <a href="gallery.php" class="hover:text-red-400 transition-colors">Gallery</a>
                    <svg class="w-3 h-3 mx-2 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
                        <path
                            d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z" />
                    </svg>
                </li>
                <li class="flex items-center">
                    <a href="collection.php" class="hover:text-red-400 transition-colors">Collections</a>
                    <?php if ($artType): ?>
                        <svg class="w-3 h-3 mx-2 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
                            <path
                                d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z" />
                        </svg>
                    <?php endif; ?>
                </li>
                <?php if ($artType): ?>
                    <li class="flex items-center">
                        <span class="text-gray-200"><?= htmlspecialchars($artType['label']) ?></span>
                    </li>
                <?php endif; ?>
            </ol>
        </nav>

        <?php if ($artType): ?>
            <header class="mb-12 text-center md:text-left">
                <h1 class="text-4xl md:text-6xl font-bold font-display uppercase tracking-wider mb-4"
                    style="color: <?= htmlspecialchars($artType['colorValue'] ?? '#ef4444') ?>;">
                    <?= htmlspecialchars($artType['label']) ?> Collection
                </h1>
                <p class="text-gray-400 text-lg max-w-3xl">Dive deep into our curated selection of articles, resources, and
                    highlights specific to <?= htmlspecialchars($artType['label']) ?> art.</p>
            </header>

            <?php
            // Layout customization based on specific art types
            $artLabelStr = strtolower($artType['label']);
            ?>

            <?php if (strpos($artLabelStr, 'photo') !== false || strpos($artLabelStr, 'digital') !== false): ?>
                <!-- Photography / Digital Art Custom Masonry Layout -->
                <div class="mb-12">
                    <h2 class="text-2xl font-bold mb-6 border-b border-white/20 pb-2">Featured Visuals</h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="h-48 bg-gray-800 rounded-lg animate-pulse w-full border border-white/5"></div>
                        <div
                            class="h-64 bg-gray-800 rounded-lg animate-pulse w-full col-span-2 row-span-2 border border-white/5">
                        </div>
                        <div class="h-32 bg-gray-800 rounded-lg animate-pulse w-full border border-white/5"></div>
                        <div class="h-48 bg-gray-800 rounded-lg animate-pulse w-full border border-white/5"></div>
                        <div class="h-32 bg-gray-800 rounded-lg animate-pulse w-full border border-white/5"></div>
                    </div>
                </div>
            <?php else: ?>
                <!-- Standard Carousel/Grid Layout for other types -->
                <div class="mb-12">
                    <h2 class="text-2xl font-bold mb-6 border-b border-white/20 pb-2">Art Spotlights</h2>
                    <div class="flex overflow-x-auto gap-6 pb-4 snap-x">
                        <?php for ($i = 1; $i <= 4; $i++): ?>
                            <div
                                class="min-w-[250px] md:min-w-[300px] h-40 bg-gray-800 rounded-lg snap-center flex items-center justify-center border border-white/5 relative group cursor-pointer overflow-hidden">
                                <span class="text-gray-500 group-hover:opacity-0 transition-opacity">Placeholder <?= $i ?></span>
                                <div
                                    class="absolute inset-0 bg-red-600/10 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                    <span class="text-red-400 font-bold">View Art</span>
                                </div>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>
            <?php endif; ?>

            <section class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Articles List -->
                <div class="lg:col-span-2 space-y-8">
                    <div
                        class="flex flex-col md:flex-row justify-between items-start md:items-center border-b border-white/20 pb-4 gap-4">
                        <h2 class="text-2xl font-bold">Featured Articles</h2>
                        <div class="flex items-center gap-2 w-full md:w-auto">
                            <div class="relative w-full md:w-64">
                                <input type="text" placeholder="Search articles..."
                                    class="w-full bg-gray-800/50 border border-white/10 rounded-full py-1.5 pl-8 pr-3 text-sm focus:outline-none focus:border-red-500">
                                <svg class="w-3.5 h-3.5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                    <path fill="currentColor"
                                        d="M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376c-34.4 25.2-76.8 40-122.7 40C93.1 416 0 322.9 0 208S93.1 0 208 0S416 93.1 416 208zM208 352a144 144 0 1 0 0-288 144 144 0 1 0 0 288z" />
                                </svg>
                            </div>
                            <button
                                class="bg-gray-800 border border-white/10 hover:bg-gray-700 p-1.5 rounded-full transition-colors"
                                title="Filter options">
                                <svg class="w-4 h-4 text-gray-300" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                    <path fill="currentColor"
                                        d="M3.9 54.9C10.5 40.9 24.5 32 40 32H472c15.5 0 29.5 8.9 36.1 22.9s4.6 30.5-5.2 42.5L320 320.9V448c0 12.1-6.8 23.2-17.7 28.6s-23.8 4.3-33.5-3l-64-48c-8.1-6-12.8-15.5-12.8-25.6V320.9L9.1 97.4C-.7 85.4-1.9 68.9 3.9 54.9z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex gap-2 overflow-x-auto pb-2 scrollbar-hide">
                        <button
                            class="px-3 py-1 bg-red-600 text-white rounded-full text-xs font-medium whitespace-nowrap">All</button>
                        <button
                            class="px-3 py-1 bg-gray-800 hover:bg-gray-700 text-gray-300 rounded-full text-xs font-medium border border-white/5 whitespace-nowrap transition-colors">History</button>
                        <button
                            class="px-3 py-1 bg-gray-800 hover:bg-gray-700 text-gray-300 rounded-full text-xs font-medium border border-white/5 whitespace-nowrap transition-colors">Technique</button>
                        <button
                            class="px-3 py-1 bg-gray-800 hover:bg-gray-700 text-gray-300 rounded-full text-xs font-medium border border-white/5 whitespace-nowrap transition-colors">Interviews</button>
                    </div>

                    <?php
                    if (!empty($articles)) {
                        foreach ($articles as $article) {
                            echo renderArticle($article, $isLoggedIn);
                        }
                    } else {
                        echo '<div class="bg-gray-800/50 rounded-xl p-8 text-center border border-white/5"><p class="text-gray-400">No articles found for this collection.</p></div>';
                    }
                    ?>

                    <div class="flex justify-center mt-6">
                        <button
                            class="px-6 py-2 bg-transparent border border-white/20 hover:border-white/50 hover:bg-white/5 rounded-full text-sm font-medium transition-all duration-300">
                            Load More Articles
                        </button>
                    </div>
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
                                <?php if ($isLoggedIn): ?>
                                    <a href="#" class="block group">
                                        <h4 class="font-medium group-hover:text-red-400 transition-colors">Digital Archive <span
                                                class="bg-red-500/20 text-red-400 text-[10px] px-1.5 py-0.5 rounded ml-2 align-middle">Member</span>
                                        </h4>
                                        <p class="text-sm text-gray-500">Thousands of high-res images.</p>
                                    </a>
                                <?php else: ?>
                                    <div class="block opacity-50 cursor-not-allowed group relative pb-2"
                                        title="Sign in to access">
                                        <h4 class="font-medium">Digital Archive <span
                                                class="bg-gray-700 text-gray-400 text-[10px] px-1.5 py-0.5 rounded ml-2 align-middle">Locked</span>
                                        </h4>
                                        <p class="text-sm text-gray-500">Thousands of high-res images.</p>
                                        <a href="../auth/sign-in.php"
                                            class="absolute inset-0 z-10 hidden group-hover:flex items-center justify-center bg-gray-900/80 text-white text-sm font-medium rounded backdrop-blur-sm">Sign
                                            In Required</a>
                                    </div>
                                <?php endif; ?>
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

                    <div class="bg-gray-800/80 rounded-xl p-6 border border-white/10">
                        <h3 class="font-bold text-lg mb-3">Popular Topics</h3>
                        <div class="flex flex-wrap gap-2">
                            <a href="#"
                                class="px-2.5 py-1 bg-gray-700 hover:bg-red-600/80 text-xs rounded transition-colors text-gray-300 hover:text-white">Renaissance</a>
                            <a href="#"
                                class="px-2.5 py-1 bg-gray-700 hover:bg-red-600/80 text-xs rounded transition-colors text-gray-300 hover:text-white">Abstract</a>
                            <a href="#"
                                class="px-2.5 py-1 bg-gray-700 hover:bg-red-600/80 text-xs rounded transition-colors text-gray-300 hover:text-white">Modern</a>
                            <a href="#"
                                class="px-2.5 py-1 bg-gray-700 hover:bg-red-600/80 text-xs rounded transition-colors text-gray-300 hover:text-white">Techniques</a>
                            <a href="#"
                                class="px-2.5 py-1 bg-gray-700 hover:bg-red-600/80 text-xs rounded transition-colors text-gray-300 hover:text-white">Galleries</a>
                        </div>
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

            <section class="mt-20 border-t border-white/10 pt-12">
                <h2 class="text-2xl font-bold mb-6 text-center">Explore Other Collections</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <?php
                    $otherCount = 0;
                    foreach ($allArtTypes as $otherType):
                        if ($otherType['id'] == $artTypeId)
                            continue;
                        if ($otherCount >= 3)
                            break;
                        $otherCount++;
                        ?>
                        <a href="?type=<?= urlencode($otherType['id']) ?>"
                            class="group block bg-gray-800/40 hover:bg-gray-800 rounded-xl overflow-hidden border border-white/5 transition-all hover:border-white/20 hover:-translate-y-1">
                            <div class="h-2 w-full"
                                style="background-color: <?= htmlspecialchars($otherType['colorValue'] ?? '#ef4444') ?>;"></div>
                            <div class="p-4 text-center">
                                <h3 class="font-bold"><?= htmlspecialchars($otherType['label']) ?></h3>
                            </div>
                        </a>
                    <?php endforeach; ?>
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
                                <?= htmlspecialchars(strtolower($type['label'])) ?>.
                            </p>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
</body>

</html>