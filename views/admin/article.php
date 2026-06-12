<?php
require_once __DIR__ . '/../../controllers/ArticleController.php';
require_once __DIR__ . '/../../controllers/UserController.php';
require_once __DIR__ . '/../../controllers/ArtTypeController.php';

$articleController = new ArticleController();
$userController = new UserController();
$artTypeController = new ArtTypeController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $articleController->delete($_POST['id']);
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

$articles = $articleController->getAll();
$users = $userController->getAll();
$artTypes = $artTypeController->getAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Article Management</title>
    <script src="../../assets/js/tailwind.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
</head>

<body class="flex flex-col flex-1 h-screen w-screen overflow-hidden">
    <section class="flex flex-row flex-1 overflow-hidden">
        <div class="flex flex-col">
            <nav class="flex flex-col w-[15vw] h-full p-4">
                <?php
                require_once __DIR__ . "/../../components/admin/nav.php";
                echo navLayout("article", "../../");
                ?>
            </nav>
        </div>
        <main class="flex flex-col flex-1 overflow-hidden w-full relative group/main">
            <header class="p-4">
                <?php
                require_once __DIR__ . "/../../components/admin/header.php";
                echo headerLayout();
                ?>
            </header>
            <div class="overflow-auto container mx-auto p-8 bg-gray-50 flex-1 relative">
                <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Article Management</h1>
                        <p class="mt-1 text-sm text-gray-500">Manage all articles and their details.</p>
                    </div>
                    <a href="article/create.php"
                        class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-300">
                        + Add New Article
                    </a>
                </div>

                <!-- Table -->
                <div class="mt-8 bg-white border border-gray-200 rounded-lg shadow-sm">
                    <table class="w-full text-left text-sm text-gray-600">
                        <thead class="bg-gray-50 border-b border-gray-200 text-gray-900">
                            <tr>
                                <th class="px-6 py-4 font-medium">ID</th>
                                <th class="px-6 py-4 font-medium">Title</th>
                                <th class="px-6 py-4 font-medium">Author</th>
                                <th class="px-6 py-4 font-medium">Art Type</th>
                                <th class="px-6 py-4 font-medium">Published</th>
                                <th class="px-6 py-4 font-medium text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($articles as $article): ?>
                                <?php
                                $author = array_filter($users, fn($u) => $u['id'] == $article['authorId']);
                                $author = reset($author);
                                $artType = array_filter($artTypes, fn($a) => $a['id'] == $article['artTypeId']);
                                $artType = reset($artType);
                                ?>
                                <tr class="hover:bg-gray-50 transition relative">
                                    <td class="px-6 py-4"><?= htmlspecialchars($article['id']) ?></td>
                                    <td class="px-6 py-4 font-medium text-gray-900">
                                        <?= htmlspecialchars($article['title']) ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <?= $author ? htmlspecialchars($author['firstName'] . ' ' . $author['lastName']) : 'Unknown' ?>
                                    </td>
                                    <td class="px-6 py-4"><?= $artType ? htmlspecialchars($artType['label']) : 'N/A' ?></td>
                                    <td class="px-6 py-4 text-sm">
                                        <?= htmlspecialchars(date('M d, Y', strtotime($article['publishedAt']))) ?>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="inline-block relative">
                                            <button onclick="toggleDropdown(<?= $article['id'] ?>)"
                                                class="p-2 text-gray-400 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition">
                                                <i data-lucide="more-vertical" class="w-4 h-4"></i>
                                            </button>
                                            <!-- dropdown -->
                                            <div id="dropdown-<?= $article['id'] ?>"
                                                class="hidden absolute right-0 top-full mt-1 z-20 w-36 bg-white rounded-lg shadow-lg border border-gray-100 py-1 text-left overflow-hidden">
                                                <a href="article/edit.php?id=<?= $article['id'] ?>"
                                                    class="w-full px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 text-left flex items-center gap-2 transition block">
                                                    <i data-lucide="edit" class="w-4 h-4 text-gray-400"></i> Edit
                                                </a>
                                                <button onclick="openDialog(<?= $article['id'] ?>)"
                                                    class="w-full px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 text-left flex items-center gap-2 transition">
                                                    <i data-lucide="trash-2" class="w-4 h-4 text-red-400"></i> Delete
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>

                            <?php if (empty($articles)): ?>
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                        No articles found. Create one to get started.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>

        </main>
    </section>

    <!-- Delete Confirmation Dialog -->
    <div id="delete-dialog"
        class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-sm scale-95 opacity-0 transition-all duration-200"
            id="dialog-content">
            <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mb-4">
                <i data-lucide="alert-triangle" class="w-6 h-6 text-red-600"></i>
            </div>
            <h2 class="text-xl font-bold text-gray-900 mb-2">Delete Article?</h2>
            <p class="text-gray-500 text-sm mb-6 leading-relaxed">Are you sure you want to delete this article? This
                action cannot be undone.</p>

            <form method="POST" class="flex items-center justify-end gap-3">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" id="delete-id" value="">
                <button type="button" onclick="closeDialog()"
                    class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition focus:ring-4 focus:ring-gray-200">Cancel</button>
                <button type="submit"
                    class="px-5 py-2.5 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition focus:ring-4 focus:ring-red-600/20">Delete</button>
            </form>
        </div>
    </div>

    <script src="../../assets/js/lucide.js"></script>
    <script>
        lucide.createIcons();

        // Dropdown Logic
        function toggleDropdown(id) {
            document.querySelectorAll('[id^="dropdown-"]').forEach(el => {
                if (el.id !== 'dropdown-' + id) el.classList.add('hidden');
            });
            document.getElementById('dropdown-' + id).classList.toggle('hidden');
        }

        document.addEventListener('click', (e) => {
            if (!e.target.closest('.inline-block.relative')) {
                document.querySelectorAll('[id^="dropdown-"]').forEach(el => el.classList.add('hidden'));
            }
        });

        // Dialog Logic
        const dialog = document.getElementById('delete-dialog');
        const dialogContent = document.getElementById('dialog-content');

        function openDialog(id) {
            document.getElementById('delete-id').value = id;
            dialog.classList.remove('hidden');
            dialog.classList.add('flex');

            requestAnimationFrame(() => {
                dialogContent.classList.remove('scale-95', 'opacity-0');
                dialogContent.classList.add('scale-100', 'opacity-100');
            });

            // Close dropdowns
            document.querySelectorAll('[id^="dropdown-"]').forEach(el => el.classList.add('hidden'));
        }

        function closeDialog() {
            dialogContent.classList.remove('scale-100', 'opacity-100');
            dialogContent.classList.add('scale-95', 'opacity-0');

            setTimeout(() => {
                dialog.classList.add('hidden');
                dialog.classList.remove('flex');
            }, 200);
        }

        // Click outside dialog to close
        dialog.addEventListener('click', (e) => {
            if (e.target === dialog) closeDialog();
        });
    </script>
</body>

</html>