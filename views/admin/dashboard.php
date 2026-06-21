<?php
session_start();
require_once __DIR__ . '/../../lib/authHelper.php';
require_once __DIR__ . '/../../controllers/StatController.php';
require_once __DIR__ . '/../../controllers/UserController.php';
require_once __DIR__ . '/../../controllers/ArticleController.php';

requireAdmin();

$statController = new StatController();
$userController = new UserController();
$articleController = new ArticleController();

$stats = $statController->getDashboardStats();
$users = array_slice($userController->getAll(), 0, 5); // Get latest 5
$articles = array_slice($articleController->getAll(), 0, 5); // Get latest 5

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="../../assets/js/tailwind.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .tab-content { display: none; }
        .tab-content.active { display: block; }
    </style>
</head>

<body class="flex flex-col flex-1 h-screen w-screen overflow-hidden bg-gray-50">
    <section class="flex flex-row flex-1 overflow-hidden">
        <div class="flex flex-col">
            <nav class="flex flex-col w-[15vw] h-full p-4 bg-white border-r border-gray-200">
                <?php
                require_once __DIR__ . "/../../components/admin/nav.php";
                echo navLayout("dashboard", "../../");
                ?>
            </nav>
        </div>
        <main class="flex flex-col flex-1 overflow-hidden w-full">
            <header class="p-4 bg-white border-b border-gray-200">
                <?php
                require_once __DIR__ . "/../../components/admin/header.php";
                echo headerLayout();
                ?>
            </header>
            
            <div class="overflow-auto container mx-auto p-8 flex-1">
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900">Dashboard Overview</h1>
                    <p class="mt-2 text-sm text-gray-500">Welcome back. Here is what is happening across your platform today.</p>
                </div>

                <!-- Tabs -->
                <div class="border-b border-gray-200 mb-6">
                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                        <button onclick="switchTab('overview')" id="tab-overview" class="border-indigo-500 text-indigo-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition cursor-pointer">
                            Overview
                        </button>
                        <button onclick="switchTab('recent')" id="tab-recent" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition cursor-pointer">
                            Recent Activity
                        </button>
                    </nav>
                </div>

                <!-- Tab: Overview -->
                <div id="content-overview" class="tab-content active">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6 mb-6">
                        <!-- Stat Card 1 -->
                        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                                <i data-lucide="users"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Total Users</p>
                                <p class="text-2xl font-bold text-gray-900"><?= htmlspecialchars($stats['users']) ?></p>
                            </div>
                        </div>

                        <!-- Stat Card 2 -->
                        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center text-purple-600">
                                <i data-lucide="newspaper"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Total Articles</p>
                                <p class="text-2xl font-bold text-gray-900"><?= htmlspecialchars($stats['articles']) ?></p>
                            </div>
                        </div>

                        <!-- Stat Card 3 -->
                        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600">
                                <i data-lucide="eye"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Article Views</p>
                                <p class="text-2xl font-bold text-gray-900"><?= htmlspecialchars($stats['article_views']) ?></p>
                            </div>
                        </div>

                        <!-- Stat Card 4 -->
                        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600">
                                <i data-lucide="hard-drive"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Storage Used</p>
                                <p class="text-2xl font-bold text-gray-900"><?= htmlspecialchars($statController->formatStorage($stats['storage_bytes'])) ?></p>
                            </div>
                        </div>

                        <!-- Stat Card 5 (Old KPI) -->
                        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-600">
                                <i data-lucide="briefcase"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Partners</p>
                                <p class="text-2xl font-bold text-gray-900"><?= htmlspecialchars($stats['partners']) ?></p>
                            </div>
                        </div>

                        <!-- Stat Card 6 (Old KPI) -->
                        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center text-red-600">
                                <i data-lucide="message-square"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Total Msgs</p>
                                <p class="text-2xl font-bold text-gray-900"><?= htmlspecialchars($stats['messages']) ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Secondary Stats Row -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        
                        <!-- Msgs Today -->
                        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center text-red-600">
                                <i data-lucide="message-square"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Msgs Today</p>
                                <p class="text-2xl font-bold text-gray-900"><?= htmlspecialchars($stats['messages_today']) ?></p>
                            </div>
                        </div>

                        <!-- Msgs Month -->
                        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-orange-100 flex items-center justify-center text-orange-600">
                                <i data-lucide="calendar"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Msgs Month</p>
                                <p class="text-2xl font-bold text-gray-900"><?= htmlspecialchars($stats['messages_month']) ?></p>
                            </div>
                        </div>

                        <!-- Msgs Year -->
                        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-600">
                                <i data-lucide="calendar-check"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Msgs Year</p>
                                <p class="text-2xl font-bold text-gray-900"><?= htmlspecialchars($stats['messages_year']) ?></p>
                            </div>
                        </div>

                        <!-- Total Locations -->
                        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-pink-100 flex items-center justify-center text-pink-600">
                                <i data-lucide="map-pin"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Locations Map</p>
                                <p class="text-2xl font-bold text-gray-900"><?= htmlspecialchars($stats['locations']) ?></p>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Tab: Recent Activity -->
                <div id="content-recent" class="tab-content">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Recent Users -->
                        <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                                <h2 class="text-lg font-medium text-gray-900">Recent Users</h2>
                                <a href="users.php" class="text-sm text-indigo-600 hover:text-indigo-900 font-medium">View all</a>
                            </div>
                            <div class="divide-y divide-gray-200">
                                <?php foreach ($users as $user): ?>
                                <div class="px-6 py-4 flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 font-bold">
                                        <?= strtoupper(substr($user['firstName'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900"><?= htmlspecialchars($user['firstName'] . ' ' . $user['lastName']) ?></p>
                                        <p class="text-xs text-gray-500"><?= htmlspecialchars($user['email']) ?></p>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                                <?php if(empty($users)): ?>
                                    <p class="px-6 py-4 text-sm text-gray-500">No users found.</p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Recent Articles -->
                        <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                                <h2 class="text-lg font-medium text-gray-900">Recent Articles</h2>
                                <a href="article.php" class="text-sm text-indigo-600 hover:text-indigo-900 font-medium">View all</a>
                            </div>
                            <div class="divide-y divide-gray-200">
                                <?php foreach ($articles as $article): ?>
                                <div class="px-6 py-4 flex items-center gap-4">
                                    <div class="w-12 h-12 rounded bg-gray-100 flex-shrink-0 flex items-center justify-center overflow-hidden">
                                        <?php if(isset($article['coverPath']) && $article['coverPath']): ?>
                                            <img src="../../<?= htmlspecialchars($article['coverPath']) ?>" class="w-full h-full object-cover" />
                                        <?php else: ?>
                                            <i data-lucide="image" class="text-gray-400"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate"><?= htmlspecialchars($article['title']) ?></p>
                                        <p class="text-xs text-gray-500">Published: <?= htmlspecialchars(date('M d, Y', strtotime($article['publishedAt']))) ?></p>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                                <?php if(empty($articles)): ?>
                                    <p class="px-6 py-4 text-sm text-gray-500">No articles found.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </section>

    <script src="../../assets/js/lucide.js"></script>
    <script>
        lucide.createIcons();

        function switchTab(tabId) {
            // Hide all contents
            document.querySelectorAll('.tab-content').forEach(el => {
                el.classList.remove('active');
            });
            // Show selected content
            document.getElementById('content-' + tabId).classList.add('active');

            // Reset all tabs
            document.querySelectorAll('nav[aria-label="Tabs"] button').forEach(btn => {
                btn.className = "border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition cursor-pointer";
            });
            // Highlight selected tab
            document.getElementById('tab-' + tabId).className = "border-indigo-500 text-indigo-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition cursor-pointer";
        }
    </script>
</body>

</html>
