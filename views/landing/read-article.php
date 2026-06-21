<?php
session_start();
require_once __DIR__ . "/../../controllers/AuthController.php";
require_once __DIR__ . '/../../controllers/ArticleController.php';
require_once __DIR__ . '/../../controllers/UserController.php';
require_once __DIR__ . '/../../controllers/ArtTypeController.php';
require_once __DIR__ . '/../../controllers/CounterController.php';
require_once __DIR__ . '/../../lib/mdToHTML.php';

$isLoggedIn = isset($_SESSION['user_id']);

$articleId = $_GET['id'] ?? null;
$articleController = new ArticleController();
$userController = new UserController();
$artTypeController = new ArtTypeController();

if (!$articleId) {
    header("Location: collection.php");
    exit;
}

$article = $articleController->getById($articleId);
if (!$article) {
    header("Location: collection.php");
    exit;
}

$author = $userController->getById($article['authorId']);
$artType = $artTypeController->getById($article['artTypeId']);

// Increment article view counter
$counterController = new CounterController();
$counterController->increment('article', $articleId);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($article['title']) ?> - JemisArt</title>
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

    <main class="pt-36 pb-16 max-w-4xl mx-auto px-4 md:px-8">
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
                    <svg class="w-3 h-3 mx-2 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
                        <path
                            d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z" />
                    </svg>
                </li>
                <li class="flex items-center">
                    <a href="collection.php?type=<?= urlencode($article['artTypeId']) ?>"
                        class="hover:text-red-400 transition-colors"><?= htmlspecialchars($artType ? $artType->getLabel() : 'Art') ?></a>
                    <svg class="w-3 h-3 mx-2 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
                        <path
                            d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z" />
                    </svg>
                </li>
                <li>
                    <span class="text-gray-200 truncate"><?= htmlspecialchars($article['title']) ?></span>
                </li>
            </ol>
        </nav>

        <!-- Article Header -->
        <header class="mb-12 border-b border-white/10 py-8">
            <div class="flex items-center gap-3 mb-4">
                <span
                    class="text-xs font-semibold <?= $variantColorClass ?> uppercase tracking-wider"><?= htmlspecialchars($article['variant']) ?></span>
                <?php if ($isLoggedIn): ?>
                    <button class='text-gray-500 hover:text-red-500 transition-colors' title='Save to profile'>
                        <svg class='w-4 h-4 fill-current' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 384 512'>
                            <path
                                d='M0 482.47V48c0-26.51 21.49-48 48-48h288c26.51 0 48 21.49 48 48v434.47c0 23.36-24.81 37.74-44.5 25.1L192 396.93 44.5 507.57C24.81 520.21 0 505.83 0 482.47z' />
                        </svg>
                    </button>
                <?php endif; ?>
            </div>

            <h1 class="text-4xl md:text-5xl font-bold font-display uppercase tracking-wider mb-4">
                <?= htmlspecialchars($article['title']) ?>
            </h1>

            <p class="text-gray-400 text-lg mb-6">
                <?= htmlspecialchars($article['description']) ?>
            </p>

            <!-- Article Metadata -->
            <div class="flex flex-wrap items-center gap-4 text-sm text-gray-400">
                <div class="flex items-center gap-2">
                    <div
                        class="w-10 h-10 rounded-full bg-gradient-to-br from-red-500 to-purple-600 flex items-center justify-center">
                        <span
                            class="text-white font-bold"><?= substr($author->getFirstName()[0] ?? '', 0, 1) . substr($author->getLastName()[0] ?? '', 0, 1) ?></span>
                    </div>
                    <div>
                        <p class="text-white font-medium">

                            <?= htmlspecialchars($author->getFirstName() ?? '') . ' ' . htmlspecialchars($author->getLastName() ?? '') ?>
                        </p>
                    </div>
                </div>
                <span class="hidden sm:inline">•</span>
                <time datetime="<?= htmlspecialchars($article['publishedAt']) ?>" class="hidden sm:inline">
                    <?= htmlspecialchars(date('M d, Y', strtotime($article['publishedAt']))) ?>
                </time>
            </div>
        </header>

        <!-- Article Content -->
        <article class="prose prose-invert max-w-none">
            <div class="space-y-6">
                <?php echo markdownToHtml($article['content']); ?>
            </div>
        </article>

        <!-- Article Footer -->
        <footer class="mt-16 pt-8 border-t border-white/10">
            <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
                <div class="flex items-center gap-3">
                    <span class="text-gray-400 text-sm">Share:</span>
                    <a href="#" class="text-gray-400 hover:text-red-400 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2s9 5 20 5a9.5 9.5 0 00-9-5.5c4.75 2.25 7-7 7-7" />
                        </svg>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-red-400 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M18 2h-3a6 6 0 00-6 6v3H7v4h2v8h4v-8h3l1-4h-4V8a2 2 0 012-2h3z" />
                        </svg>
                    </a>
                </div>
                <a href="collection.php?type=<?= urlencode($article['artTypeId']) ?>"
                    class="px-4 py-2 bg-gray-800 hover:bg-gray-700 rounded-lg text-sm font-medium transition-colors">
                    Back to Collection
                </a>
            </div>

            <!-- Related Articles Hint -->
            <div class="bg-gray-800/50 rounded-xl p-6 border border-white/5">
                <h3 class="font-bold mb-3">More from

                    <?= htmlspecialchars($artType ? $artType->getLabel() : 'this collection') ?>
                </h3>
                <p class="text-gray-400 text-sm mb-4">Explore more articles and resources in this category.</p>
                <a href="collection.php?type=<?= urlencode($article['artTypeId']) ?>"
                    class="text-red-400 hover:text-red-300 font-medium inline-flex items-center gap-1 text-sm">
                    View Collection &rarr;
                </a>
            </div>
        </footer>
    </main>
</body>

</html>