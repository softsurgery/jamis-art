<?php
session_start();
require_once __DIR__ . '/../../lib/authHelper.php';
require_once __DIR__ . '/../../controllers/ResourceController.php';
require_once __DIR__ . '/../../controllers/ArtTypeController.php';
require_once __DIR__ . '/../../controllers/UploadController.php';
require_once __DIR__ . '/../../constants/upload-group-map.php';

requireAdmin();

$resourceController = new ResourceController();
$artTypeController = new ArtTypeController();

$resourceUploadGroupId = getGroupIdByName("resources");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action === 'delete') {
            $resourceController->delete($_POST['id']);
            header("Location: " . $_SERVER['PHP_SELF'] . (isset($_POST['filterArtTypeId']) && $_POST['filterArtTypeId'] !== '' ? "?type=" . $_POST['filterArtTypeId'] : ""));
            exit;
        }
    }
}

$artTypes = $artTypeController->getAll();
$selectedArtTypeId = isset($_GET['type']) ? $_GET['type'] : null;

if ($selectedArtTypeId) {
    $resources = $resourceController->getAllByArtType($selectedArtTypeId);
} else {
    $resources = $resourceController->getAll();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resources Management</title>
    <script src="../../assets/js/tailwind.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
</head>

<body class="flex flex-col flex-1 h-screen w-screen overflow-hidden font-poppins">
    <section class="flex flex-row flex-1 overflow-hidden">
        <div class="flex flex-col">
            <nav class="flex flex-col w-[15vw] h-full p-4">
                <?php
                require_once __DIR__ . "/../../components/admin/nav.php";
                echo navLayout("resources", "../../");
                ?>
            </nav>
        </div>
        <main class="flex flex-col flex-1 overflow-hidden w-full">
            <header class="p-4">
                <?php
                require_once __DIR__ . "/../../components/admin/header.php";
                echo headerLayout();
                ?>
            </header>
            <div class="overflow-auto container mx-auto p-8 bg-gray-50 flex-1 flex flex-col relative">
                <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Resources</h1>
                        <p class="mt-1 text-sm text-gray-500">Manage uploaded resources for each art type.</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <form method="GET" action="" class="flex items-center gap-2">
                            <label for="type" class="text-sm font-medium text-gray-700">Filter by Art Type:</label>
                            <select name="type" id="type" onchange="this.form.submit()"
                                class="block w-48 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 bg-white border outline-none transition">
                                <option value="">-- All Art Types --</option>
                                <?php foreach ($artTypes as $artType): ?>
                                    <option value="<?= htmlspecialchars($artType['id']) ?>"
                                        <?= $selectedArtTypeId == $artType['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($artType['label']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </form>
                        <a href="resources/create.php<?= $selectedArtTypeId ? '?type=' . urlencode($selectedArtTypeId) : '' ?>" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm font-medium transition shadow-sm inline-block">
                            Add Resource
                        </a>>
                    </div>
                </div>

                <div class="mt-6 flex-1 bg-white border border-gray-200 rounded-lg shadow-sm relative overflow-hidden flex flex-col">
                    <?php if (count($resources) > 0): ?>
                        <div class="overflow-x-auto flex-1">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50 sticky top-0">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Label</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Art Type</th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($resources as $res): 
                                        // Find art type label
                                        $artTypeLabel = 'Unknown';
                                        foreach ($artTypes as $at) {
                                            if ($at['id'] == $res['artTypeId']) {
                                                $artTypeLabel = $at['label'];
                                                break;
                                            }
                                        }
                                    ?>
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($res['label']) ?></div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-500 truncate max-w-xs"><?= htmlspecialchars($res['description']) ?></div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                                    <?= htmlspecialchars($artTypeLabel) ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <div class="flex items-center justify-end gap-4">
                                                    <a href="resources/update.php?id=<?= $res['id'] ?>" class="text-indigo-600 hover:text-indigo-900 transition flex items-center">
                                                        <i data-lucide="edit" class="w-4 h-4 mr-1"></i> Edit
                                                    </a>
                                                    <form method="POST" action="" class="inline-block m-0" onsubmit="return confirm('Are you sure you want to delete this resource?');">
                                                        <input type="hidden" name="action" value="delete">
                                                        <input type="hidden" name="id" value="<?= $res['id'] ?>">
                                                        <input type="hidden" name="filterArtTypeId" value="<?= htmlspecialchars($selectedArtTypeId ?? '') ?>">
                                                        <button type="submit" class="text-red-500 hover:text-red-700 transition flex items-center">
                                                            <i data-lucide="trash-2" class="w-4 h-4 mr-1"></i> Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="flex flex-col items-center justify-center h-full min-h-[400px] text-gray-500">
                            <i data-lucide="folder-open" class="w-16 h-16 mb-4 text-gray-300"></i>
                            <p class="text-lg">No resources found.</p>
                            <p class="text-sm mt-1">Click "Add Resource" to create one.</p>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </main>
    </section>

    <script src="../../assets/js/lucide.js"></script>
    <script>
        lucide.createIcons();
    </script>
</body>

</html>
