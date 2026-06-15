<?php
session_start();
require_once __DIR__ . '/../controllers/ArticleController.php';
require_once __DIR__ . '/../views/landing/collection/article.php';

$isLoggedIn = isset($_SESSION['user_id']);
$artTypeId = $_GET['type'] ?? null;
$search = $_GET['search'] ?? '';
$filter = $_GET['filter'] ?? 'All';
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;

$articleController = new ArticleController();
// Fetch limit + 1 to check if there are more articles
$articles = $articleController->getFilteredArticles($artTypeId, $search, $filter, $offset, $limit + 1);

$hasMore = count($articles) > $limit;
if ($hasMore) {
    array_pop($articles);
}

$html = '';
if (!empty($articles)) {
    foreach ($articles as $article) {
        $html .= renderArticle($article, $isLoggedIn);
    }
} else if ($offset === 0) {
    $html = '<div class="bg-gray-800/50 rounded-xl p-8 text-center border border-white/5 w-full"><p class="text-gray-400">No articles found matching your criteria.</p></div>';
}

header('Content-Type: application/json');
echo json_encode([
    'html' => $html,
    'hasMore' => $hasMore
]);
