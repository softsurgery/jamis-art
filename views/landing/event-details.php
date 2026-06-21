<?php
session_start();
require_once __DIR__ . "/../../controllers/AuthController.php";
require_once __DIR__ . '/../../controllers/EventController.php';
require_once __DIR__ . '/../../controllers/ArtTypeController.php';
require_once __DIR__ . '/../../lib/mdToHTML.php';

$isLoggedIn = isset($_SESSION['user_id']);

$eventId = $_GET['id'] ?? null;
$eventController = new EventController();
$artTypeController = new ArtTypeController();

if (!$eventId) {
    header("Location: ../../index.php");
    exit;
}

$event = $eventController->getById($eventId);
if (!$event) {
    header("Location: ../../index.php");
    exit;
}

$artType = $artTypeController->getById($event['artTypeId']);
$colorValue = $artType ? $artType->getColorValue() : '#ffffff';
$artTypeLabel = $artType ? $artType->getLabel() : 'General';
$coverUrl = !empty($event['coverPath']) ? '../../' . $event['coverPath'] : '../../assets/img/placeholder.jpg';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($event['title']) ?> - JemisArt Events</title>
    <link rel="stylesheet" href="../../assets/styles.css" />
    <script src="../../assets/js/tailwind.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
</head>

<body class="bg-gray-900 text-white min-h-screen font-body relative">
    
    <!-- Dynamic background glow -->
    <div class="fixed top-0 left-0 w-full h-full pointer-events-none z-[-1] overflow-hidden">
        <div class="absolute top-[-20%] right-[-10%] w-[800px] h-[800px] rounded-full blur-[180px] opacity-20" style="background-color: <?= $colorValue ?>;"></div>
    </div>

    <nav class="fixed top-0 left-0 w-full z-50 glass border-b border-white/10">
        <?php
        require_once __DIR__ . '/../../components/landing/nav.php';
        echo landingNavLayout('', $isLoggedIn, './../..');
        ?>
    </nav>

    <main class="pt-36 pb-16 max-w-4xl mx-auto px-4 md:px-8">
        <!-- Breadcrumbs -->
        <nav class="text-sm text-gray-400 mb-8" aria-label="Breadcrumb">
            <ol class="list-none p-0 inline-flex">
                <li class="flex items-center">
                    <a href="../../index.php" class="hover:text-white transition-colors">Home</a>
                    <svg class="w-3 h-3 mx-2 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
                        <path d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z" />
                    </svg>
                </li>
                <li class="flex items-center">
                    <a href="../../index.php#events" class="hover:text-white transition-colors">Events</a>
                    <svg class="w-3 h-3 mx-2 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
                        <path d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z" />
                    </svg>
                </li>
                <li>
                    <span class="text-gray-200 truncate"><?= htmlspecialchars($event['title']) ?></span>
                </li>
            </ol>
        </nav>

        <!-- Event Cover -->
        <div class="mb-10 w-full h-[400px] rounded-3xl overflow-hidden relative border border-white/10 shadow-2xl">
            <img src="<?= htmlspecialchars($coverUrl) ?>" alt="<?= htmlspecialchars($event['title']) ?>" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/40 to-transparent"></div>
            
            <div class="absolute bottom-0 left-0 w-full p-8">
                <span class="px-3 py-1 text-xs font-bold uppercase tracking-wider rounded-full backdrop-blur-md bg-white/10 text-white border border-white/20 mb-4 inline-block" style="color: <?= $colorValue ?>; border-color: <?= $colorValue ?>;">
                    <?= htmlspecialchars($artTypeLabel) ?>
                </span>
                <h1 class="text-4xl md:text-6xl font-bold font-display uppercase tracking-wider mb-2 text-white drop-shadow-lg">
                    <?= htmlspecialchars($event['title']) ?>
                </h1>
                <div class="flex items-center gap-4 text-sm text-gray-300">
                    <time datetime="<?= htmlspecialchars($event['createdAt']) ?>" class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Posted on <?= htmlspecialchars(date('M d, Y', strtotime($event['createdAt']))) ?>
                    </time>
                </div>
            </div>
        </div>

        <div class="bg-gray-800/40 border border-white/5 rounded-2xl p-6 mb-12 backdrop-blur-md">
            <h2 class="text-xl font-semibold mb-2" style="color: <?= $colorValue ?>;">Event Overview</h2>
            <p class="text-gray-300 text-lg leading-relaxed">
                <?= htmlspecialchars($event['description']) ?>
            </p>
        </div>

        <!-- Event Markdown Content -->
        <article class="prose prose-invert max-w-none prose-a:text-[<?= $colorValue ?>] prose-a:no-underline hover:prose-a:underline prose-headings:font-display prose-headings:uppercase prose-headings:tracking-wider">
            <div class="space-y-6">
                <?php echo markdownToHtml($event['markdown']); ?>
            </div>
        </article>

        <!-- Back Footer -->
        <footer class="mt-16 pt-8 border-t border-white/10">
            <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
                <a href="../../index.php#events"
                    class="px-6 py-3 bg-white/5 hover:bg-white/10 border border-white/10 rounded-full text-sm font-medium transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Back to All Events
                </a>
                
                <button class="px-6 py-3 rounded-full text-sm font-medium text-white transition-all duration-300 hover:scale-105" style="background-color: <?= $colorValue ?>; box-shadow: 0 0 20px <?= $colorValue ?>40;">
                    Participate
                </button>
            </div>
        </footer>
    </main>
</body>

</html>
