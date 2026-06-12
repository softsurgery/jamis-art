<?php
require_once __DIR__ . '/../../../controllers/ArticleController.php';
require_once __DIR__ . '/../../../controllers/UserController.php';
require_once __DIR__ . '/../../../controllers/ArtTypeController.php';

$articleController = new ArticleController();
$userController = new UserController();
$artTypeController = new ArtTypeController();

$error = '';
$success = false;
$article = null;

// Get article ID from query parameter
$id = $_GET['id'] ?? null;

if (!$id) {
    $error = 'No article ID provided.';
} else {
    $article = $articleController->getById($id);
    if (!$article) {
        $error = 'Article not found.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $article) {
    try {
        $updatedArticle = new Article(
            $id,
            $_POST['title'],
            $_POST['description'],
            $_POST['content'],
            $_POST['publishedAt'],
            $_POST['authorId'],
            $_POST['variant'] ?? 'default',
            $_POST['artTypeId']
        );
        $articleController->update($updatedArticle);
        $success = true;
        $article = $updatedArticle;
    } catch (Exception $e) {
        $error = 'Error updating article: ' . $e->getMessage();
    }
}

$users = $userController->getAll();
$artTypes = $artTypeController->getAll();

// Convert datetime for input field
$publishedAtValue = '';
if ($article && isset($article['publishedAt'])) {
    $date = new DateTime($article['publishedAt']);
    $publishedAtValue = $date->format('Y-m-d\TH:i');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Article</title>
    <script src="../../../assets/js/tailwind.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
</head>

<body class="flex flex-col flex-1 h-screen w-screen overflow-hidden">
    <?php if ($success): ?>
        <script>
            setTimeout(() => {
                window.location.href = '../article.php';
            }, 1500);
        </script>
    <?php endif; ?>

    <section class="flex flex-row flex-1 overflow-hidden">
        <div class="flex flex-col">
            <nav class="flex flex-col w-[15vw] h-full p-4">
                <?php
                require_once __DIR__ . "/../../../components/admin/nav.php";
                echo navLayout("article", "../../../");
                ?>
            </nav>
        </div>
        <main class="flex flex-col flex-1 overflow-hidden w-full relative group/main">
            <header class="p-4">
                <?php
                require_once __DIR__ . "/../../../components/admin/header.php";
                echo headerLayout();
                ?>
            </header>
            <div class="overflow-auto flex-1">
                <div class="container mx-auto rounded-lg shadow-sm p-8">
                    <div class="flex items-center justify-between pb-6 border-b border-gray-200 mb-6">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Edit Article</h1>
                            <p class="mt-1 text-sm text-gray-500">Update the article details below.</p>
                        </div>
                        <a href="../article.php"
                            class="inline-flex items-center justify-center text-gray-400 hover:text-gray-800 hover:bg-gray-100 rounded-full p-2 transition">
                            <i data-lucide="x" class="w-6 h-6"></i>
                        </a>
                    </div>

                    <?php if ($error): ?>
                        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg flex gap-3">
                            <i data-lucide="alert-circle" class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5"></i>
                            <div>
                                <h3 class="font-medium text-red-900">Error</h3>
                                <p class="text-sm text-red-700"><?= htmlspecialchars($error) ?></p>
                            </div>
                        </div>
                        <div class="text-center">
                            <a href="../article.php"
                                class="inline-flex items-center justify-center px-5 py-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                                Back to Articles
                            </a>
                        </div>
                    <?php elseif ($success): ?>
                        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg flex gap-3">
                            <i data-lucide="check-circle" class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5"></i>
                            <div>
                                <h3 class="font-medium text-green-900">Success</h3>
                                <p class="text-sm text-green-700">Article updated successfully. Redirecting...</p>
                            </div>
                        </div>
                    <?php else: ?>
                        <form method="POST" class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Title <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="title" required placeholder="e.g. My Amazing Artwork"
                                    value="<?= htmlspecialchars($article['title'] ?? '') ?>"
                                    class="w-full border border-gray-300 rounded-lg p-3 text-sm outline-none focus:ring-2 focus:ring-indigo-600/50 focus:border-indigo-600 transition">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Description <span
                                        class="text-red-500">*</span></label>
                                <textarea name="description" required placeholder="Brief description of the article"
                                    class="w-full border border-gray-300 rounded-lg p-3 text-sm outline-none focus:ring-2 focus:ring-indigo-600/50 focus:border-indigo-600 transition resize-none"
                                    rows="3"><?= htmlspecialchars($article['description'] ?? '') ?></textarea>
                                <p class="mt-1 text-xs text-gray-500">A short summary that will appear as a preview.</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Content <span
                                        class="text-red-500">*</span></label>
                                <textarea name="content" required placeholder="Full article content"
                                    class="w-full border border-gray-300 rounded-lg p-3 text-sm outline-none focus:ring-2 focus:ring-indigo-600/50 focus:border-indigo-600 transition resize-none"
                                    rows="8"><?= htmlspecialchars($article['content'] ?? '') ?></textarea>
                                <p class="mt-1 text-xs text-gray-500">The main body of the article.</p>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Author <span
                                            class="text-red-500">*</span></label>
                                    <select name="authorId" required
                                        class="w-full border border-gray-300 rounded-lg p-3 text-sm outline-none focus:ring-2 focus:ring-indigo-600/50 focus:border-indigo-600 transition">
                                        <option value="">Select an author</option>
                                        <?php foreach ($users as $user): ?>
                                            <option value="<?= $user['id'] ?>" <?= $user['id'] == ($article['authorId'] ?? '') ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($user['firstName'] . ' ' . $user['lastName']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Art Type <span
                                            class="text-red-500">*</span></label>
                                    <select name="artTypeId" required
                                        class="w-full border border-gray-300 rounded-lg p-3 text-sm outline-none focus:ring-2 focus:ring-indigo-600/50 focus:border-indigo-600 transition">
                                        <option value="">Select an art type</option>
                                        <?php foreach ($artTypes as $type): ?>
                                            <option value="<?= $type['id'] ?>" <?= $type['id'] == ($article['artTypeId'] ?? '') ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($type['label']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Published Date <span
                                            class="text-red-500">*</span></label>
                                    <input type="datetime-local" name="publishedAt" required
                                        value="<?= $publishedAtValue ?>"
                                        class="w-full border border-gray-300 rounded-lg p-3 text-sm outline-none focus:ring-2 focus:ring-indigo-600/50 focus:border-indigo-600 transition">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Variant</label>
                                    <input type="text" name="variant" placeholder="e.g. featured, standard"
                                        value="<?= htmlspecialchars($article['variant'] ?? '') ?>"
                                        class="w-full border border-gray-300 rounded-lg p-3 text-sm outline-none focus:ring-2 focus:ring-indigo-600/50 focus:border-indigo-600 transition">
                                </div>
                            </div>

                            <div class="pt-6 border-t border-gray-200 flex gap-3">
                                <a href="../article.php"
                                    class="flex-1 inline-flex items-center justify-center px-5 py-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition focus:ring-4 focus:ring-gray-200">
                                    Cancel
                                </a>
                                <button type="submit"
                                    class="flex-1 inline-flex items-center justify-center px-5 py-3 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition focus:ring-4 focus:ring-indigo-600/20">
                                    Update Article
                                </button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </section>

    <script src="../../../assets/js/lucide.js"></script>
    <script>
        lucide.createIcons();
    </script>
</body>

</html>