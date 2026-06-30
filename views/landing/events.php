<?php
session_start();
require_once __DIR__ . "/../../controllers/AuthController.php";
require_once __DIR__ . '/../../controllers/ArtTypeController.php';
require_once __DIR__ . '/../../controllers/EventController.php';

$isLoggedIn = isset($_SESSION['user_id']);

$artTypeId = $_GET['type'] ?? null;

$artTypeController = new ArtTypeController();
$allArtTypes = $artTypeController->getAll();

$eventController = new EventController();

$events = [];
$currentArtType = null;

if ($artTypeId) {
    foreach ($allArtTypes as $type) {
        if ($type['id'] == $artTypeId) {
            $currentArtType = $type;
            break;
        }
    }
    // Fetch events for this specific art type
    $events = $eventController->getByArtTypeId($artTypeId);
} else {
    // Fetch all events
    $events = $eventController->getAll();
}

$headerTitle = $currentArtType ? htmlspecialchars($currentArtType['label']) . " Events" : "All Events";
$headerColor = $currentArtType ? ($currentArtType['colorValue'] ?? '#ef4444') : '#ef4444';
$headerDescription = $currentArtType
    ? "Discover upcoming actions and community gatherings for " . htmlspecialchars($currentArtType['label']) . " art."
    : "Discover all upcoming actions and community gatherings across all disciplines.";

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JemisArt - Events</title>
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
        echo landingNavLayout('events', $isLoggedIn, './../..');
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
                    <a href="events.php" class="hover:text-red-400 transition-colors">Events</a>
                    <?php if ($currentArtType): ?>
                        <svg class="w-3 h-3 mx-2 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
                            <path
                                d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z" />
                        </svg>
                    <?php endif; ?>
                </li>
                <?php if ($currentArtType): ?>
                    <li class="flex items-center">
                        <span class="text-gray-200"><?= htmlspecialchars($currentArtType['label']) ?></span>
                    </li>
                <?php endif; ?>
            </ol>
        </nav>

        <header class="mb-12 text-center md:text-left">
            <h1 class="text-4xl md:text-6xl font-bold font-display uppercase tracking-wider mb-4"
                style="color: <?= htmlspecialchars($headerColor) ?>;">
                <?= $headerTitle ?>
            </h1>
            <p class="text-gray-400 text-lg max-w-3xl"><?= $headerDescription ?></p>
        </header>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php if (empty($events)): ?>
                <div class="col-span-full text-center text-gray-500 py-12 bg-gray-800/40 rounded-xl border border-white/5">
                    No events scheduled at the moment. Check back soon.
                </div>
            <?php else: ?>
                <?php foreach ($events as $event): ?>
                    <?php
                    $coverUrl = !empty($event['coverPath']) ? '../../' . $event['coverPath'] : '../../assets/img/placeholder.jpg';
                    $artType = array_filter($allArtTypes, fn($a) => $a['id'] == $event['artTypeId']);
                    $artType = reset($artType);
                    $colorValue = $artType ? $artType['colorValue'] : '#ffffff';
                    $label = $artType ? $artType['label'] : 'General';
                    ?>
                    <div class="glass rounded-2xl overflow-hidden group cursor-pointer border border-white/5 hover:border-[<?= $colorValue ?>]/30 transition-all duration-500 flex flex-col h-full bg-gray-800/40"
                        style="--event-glow: <?= $colorValue ?>;">
                        <div class="h-48 overflow-hidden relative">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent z-10"></div>
                            <img src="<?= htmlspecialchars($coverUrl) ?>" alt="<?= htmlspecialchars($event['title']) ?>"
                                class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700">
                            <span
                                class="absolute top-4 right-4 z-20 px-3 py-1 text-xs font-bold uppercase tracking-wider rounded-full backdrop-blur-md bg-white/10 text-white border border-white/20"
                                style="color: <?= $colorValue ?>;">
                                <?= htmlspecialchars($label) ?>
                            </span>
                        </div>
                        <div class="p-6 flex flex-col flex-1 relative">
                            <div
                                class="absolute top-0 left-6 right-6 h-[1px] bg-gradient-to-r from-transparent via-[<?= $colorValue ?>]/50 to-transparent">
                            </div>
                            <h4 class="text-xl font-bold mb-3 text-white transition-colors"
                                style="--tw-text-opacity: 1; color: var(--tw-text-opacity) == 1 ? currentColor : currentColor; "
                                onmouseover="this.style.color='<?= $colorValue ?>'" onmouseout="this.style.color=''">
                                <?= htmlspecialchars($event['title']) ?>
                            </h4>
                            <p class="text-gray-400 text-sm leading-relaxed mb-6 flex-1">
                                <?= htmlspecialchars($event['description']) ?>
                            </p>
                            <div class="flex items-center justify-between text-xs text-gray-500 pt-4 border-t border-white/5">
                                <span><?= htmlspecialchars(date('M d, Y', strtotime($event['createdAt']))) ?></span>
                                <a href="event-details.php?id=<?= $event['id'] ?>"
                                    class="flex items-center gap-1 hover:text-white transition-colors">Details <svg
                                        class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                    </svg></a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </main>
</body>

</html>