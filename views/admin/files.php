<?php
session_start();
require_once __DIR__ . '/../../lib/authHelper.php';
require_once __DIR__ . '/../../controllers/UploadController.php';
require_once __DIR__ . '/../../controllers/UploadGroupController.php';

requireAdmin();

$currentFolderId = isset($_GET['folder']) ? (int) $_GET['folder'] : 0;
$viewMode = isset($_GET['view']) ? $_GET['view'] : 'grid'; // grid or list

// Get current group info
$currentGroup = null;
if ($currentFolderId !== 0) {
    $currentGroup = UploadGroupController::getById($currentFolderId);
    if (!$currentGroup) {
        $currentFolderId = 0; // Reset to root if group doesn't exist
    }
}

// Get child groups
$childGroups = UploadGroupController::getByParent($currentFolderId);

// Get files in current group
$allUploads = UploadController::findAll();
$filesInCurrentFolder = array_filter($allUploads, function ($upload) use ($currentFolderId) {
    return $upload->getId() ? ($upload->getId() ? true : false) : false;
});

// Filter files by groupeId - need to check the database directly
global $pdo;
$filesInCurrentFolder = [];
if ($currentFolderId === 0) {
    // Get files with no group (groupeId IS NULL)
    $stmt = $pdo->prepare("SELECT * FROM upload WHERE groupeId IS NULL ORDER BY slug ASC");
} else {
    // Get files in specific group
    $stmt = $pdo->prepare("SELECT * FROM upload WHERE groupeId = :groupId ORDER BY slug ASC");
    $stmt->bindValue(':groupId', $currentFolderId, PDO::PARAM_INT);
}
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($results as $row) {
    $filesInCurrentFolder[] = new Upload($row['id'], $row['slug'], $row['relativePath'], $row['mimeType'], $row['size'], $row['isTemporary'], $row['isPrivate']);
}

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'delete_file' && isset($_POST['file_id'])) {
        UploadController::delete($_POST['file_id']);
        header("Location: " . $_SERVER['PHP_SELF'] . "?folder=$currentFolderId&view=$viewMode");
        exit;
    } elseif ($_POST['action'] === 'create_folder' && isset($_POST['folder_name'])) {
        try {
            UploadGroupController::create($_POST['folder_name'], $currentFolderId);
            header("Location: " . $_SERVER['PHP_SELF'] . "?folder=$currentFolderId&view=$viewMode");
            exit;
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    } elseif ($_POST['action'] === 'delete_folder' && isset($_POST['folder_id'])) {
        try {
            UploadGroupController::delete($_POST['folder_id']);
            header("Location: " . $_SERVER['PHP_SELF'] . "?folder=$currentFolderId&view=$viewMode");
            exit;
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}
function getMimeIcon($mimeType)
{
    if (strpos($mimeType, 'image/') === 0)
        return 'image';
    if (strpos($mimeType, 'video/') === 0)
        return 'video';
    if (strpos($mimeType, 'audio/') === 0)
        return 'music';
    if (strpos($mimeType, 'application/pdf') === 0)
        return 'file-pdf';
    if (strpos($mimeType, 'text/') === 0)
        return 'file-text';
    return 'file';
}

// Helper function to format file size
function formatFileSize($bytes)
{
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= (1 << (10 * $pow));
    return round($bytes, 2) . ' ' . $units[$pow];
}

// Helper function to get thumbnail URL for images
function getThumbnailUrl($upload)
{
    if (strpos($upload->getMimetype(), 'image/') === 0) {
        return $upload->getRelativePath();
    }
    return null;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Management</title>
    <script src="../../assets/js/tailwind.js"></script>
    <script src="../../assets/js/lucide.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="../../assets/css/admin.css">
</head>

<body class="flex flex-col flex-1 h-screen w-screen overflow-hidden">
    <section class="flex flex-row flex-1 overflow-hidden">
        <div class="flex flex-col">
            <nav class="flex flex-col w-[15vw] h-full p-4">
                <?php
                require_once __DIR__ . "/../../components/admin/nav.php";
                echo navLayout("files", "../../");
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
                <!-- Header with Title and Controls -->
                <div class="flex items-center justify-between pb-4 border-b border-gray-200 mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">File Management</h1>
                        <p class="mt-1 text-sm text-gray-500">Manage all uploaded files and organize them by folder.</p>
                    </div>
                    <div class="flex gap-2">
                        <button onclick="switchView('grid')"
                            class="inline-flex items-center justify-center rounded-lg <?php echo $viewMode === 'grid' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-600 border border-gray-200'; ?> px-4 py-2 text-sm font-medium transition hover:bg-indigo-700">
                            <i data-lucide="grid" class="w-4 h-4 mr-2"></i>
                            Grid
                        </button>
                        <button onclick="switchView('list')"
                            class="inline-flex items-center justify-center rounded-lg <?php echo $viewMode === 'list' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-600 border border-gray-200'; ?> px-4 py-2 text-sm font-medium transition hover:bg-indigo-700">
                            <i data-lucide="list" class="w-4 h-4 mr-2"></i>
                            List
                        </button>
                    </div>
                </div>

                <!-- Breadcrumb Navigation -->
                <div class="mb-6 p-3 bg-white rounded-lg border border-gray-200">
                    <span class="text-sm text-gray-600">
                        <a href="?view=<?php echo $viewMode; ?>" class="text-indigo-600 hover:underline">📁 Root</a>
                        <?php
                        if ($currentFolderId !== 0 && $currentGroup) {
                            $breadcrumbs = UploadGroupController::getBreadcrumbPath($currentFolderId);
                            foreach ($breadcrumbs as $crumb) {
                                echo '<span class="text-gray-400 mx-2">/</span>';
                                echo '<a href="?folder=' . $crumb->getId() . '&view=' . $viewMode . '" class="text-indigo-600 hover:underline">' . htmlspecialchars($crumb->getName()) . '</a>';
                            }
                        }
                        ?>
                    </span>
                </div>

                <!-- Create Folder Form -->
                <div class="mb-6 p-4 bg-white rounded-lg border border-gray-200">
                    <form method="POST" class="flex gap-2">
                        <input type="hidden" name="action" value="create_folder">
                        <input type="text" name="folder_name" placeholder="New folder name..."
                            class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            required>
                        <button type="submit"
                            class="inline-flex items-center justify-center rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-green-700">
                            <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                            Create Folder
                        </button>
                    </form>
                </div>

                <!-- Main Content -->
                <?php if ($viewMode === 'grid'): ?>
                    <!-- GRID VIEW -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6">
                        <div class="grid grid-cols-[repeat(auto-fill,minmax(160px,1fr))] gap-4">
                            <!-- Folders -->
                            <?php foreach ($childGroups as $group): ?>
                                <div class="group bg-white border border-gray-200 rounded-lg overflow-hidden transition-all duration-200 cursor-pointer hover:border-indigo-600 hover:shadow-md">
                                    <a href="?folder=<?php echo $group->getId(); ?>&view=grid">
                                        <div class="w-full h-[120px] bg-gray-100 flex items-center justify-center overflow-hidden text-2xl text-gray-400">
                                            <div class="w-full h-full bg-gradient-to-br from-amber-500 to-orange-500 flex items-center justify-center">
                                                <i data-lucide="folder" class="w-12 h-12 text-white"></i>
                                            </div>
                                        </div>
                                        <div class="p-2 text-center">
                                            <div class="text-xs font-medium text-gray-800 overflow-hidden text-ellipsis whitespace-nowrap mb-1"><?php echo htmlspecialchars($group->getName()); ?></div>
                                            <div class="text-[0.7rem] text-gray-400">Folder</div>
                                        </div>
                                    </a>
                                    <div class="flex gap-2 justify-center items-center p-2 border-t border-gray-100 opacity-0 transition-opacity duration-200 w-fit group-hover:opacity-100">
                                        <button onclick="deleteFolder(<?php echo $group->getId(); ?>)"
                                            class="px-2 py-1 text-xs rounded cursor-pointer transition-all duration-200 bg-red-500 text-white hover:bg-red-600 flex-1">
                                            <i data-lucide="trash-2" class="w-3 h-3"></i>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                            <!-- Files -->
                            <?php foreach ($filesInCurrentFolder as $upload): ?>
                                <div class="group bg-white border border-gray-200 rounded-lg overflow-hidden transition-all duration-200 cursor-pointer hover:border-indigo-600 hover:shadow-md">
                                    <div class="w-full h-[120px] bg-gray-100 flex items-center justify-center overflow-hidden text-2xl text-gray-400">
                                        <?php
                                        $thumbnail = getThumbnailUrl($upload);
                                        if ($thumbnail):
                                            ?>
                                            <img src="../../<?php echo htmlspecialchars($thumbnail); ?>"
                                                alt="<?php echo htmlspecialchars($upload->getSlug()); ?>"
                                                class="w-full h-full object-cover">
                                        <?php else: ?>
                                            <i data-lucide="<?php echo getMimeIcon($upload->getMimetype()); ?>"
                                                class="w-12 h-12"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="p-2 text-center">
                                        <div class="text-xs font-medium text-gray-800 overflow-hidden text-ellipsis whitespace-nowrap mb-1" title="<?php echo htmlspecialchars($upload->getSlug()); ?>">
                                            <?php echo htmlspecialchars($upload->getSlug()); ?>
                                        </div>
                                        <div class="text-[0.7rem] text-gray-400"><?php echo formatFileSize($upload->getSize()); ?></div>
                                    </div>
                                    <div class="flex gap-2 justify-center items-center p-2 border-t border-gray-100 opacity-0 transition-opacity duration-200 w-fit group-hover:opacity-100">
                                        <a href="../../<?php echo htmlspecialchars($upload->getRelativePath()); ?>"
                                            target="_blank" class="px-2 py-1 text-xs rounded cursor-pointer transition-all duration-200 bg-blue-500 text-white hover:bg-blue-600">
                                            <i data-lucide="eye" class="w-3 h-3"></i>
                                        </a>
                                        <a href="../../<?php echo htmlspecialchars($upload->getRelativePath()); ?>" download
                                            class="px-2 py-1 text-xs rounded cursor-pointer transition-all duration-200 bg-green-500 text-white hover:bg-green-600">
                                            <i data-lucide="download" class="w-3 h-3"></i>
                                        </a>
                                        <button onclick="deleteFile(<?php echo $upload->getId(); ?>)"
                                            class="px-2 py-1 text-xs rounded cursor-pointer transition-all duration-200 bg-red-500 text-white hover:bg-red-600">
                                            <i data-lucide="trash-2" class="w-3 h-3"></i>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                            <?php if (empty($childGroups) && empty($filesInCurrentFolder)): ?>
                                <div class="col-span-full text-center py-12">
                                    <i data-lucide="inbox" class="w-12 h-12 mx-auto text-gray-400 mb-4"></i>
                                    <p class="text-gray-500">No files or folders in this location</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                <?php else: ?>
                    <!-- LIST VIEW -->
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
                        <table class="w-full text-left text-sm text-gray-600">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3 font-semibold text-gray-900">Name</th>
                                    <th class="px-6 py-3 font-semibold text-gray-900">Type</th>
                                    <th class="px-6 py-3 font-semibold text-gray-900">Size</th>
                                    <th class="px-6 py-3 font-semibold text-gray-900">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Folders -->
                                <?php foreach ($childGroups as $group): ?>
                                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                                        <td class="px-6 py-3">
                                            <a href="?folder=<?php echo $group->getId(); ?>&view=list"
                                                class="flex items-center text-indigo-600 hover:underline">
                                                <i data-lucide="folder" class="w-4 h-4 mr-2"></i>
                                                <span><?php echo htmlspecialchars($group->getName()); ?></span>
                                            </a>
                                        </td>
                                        <td class="px-6 py-3">Folder</td>
                                        <td class="px-6 py-3">-</td>
                                        <td class="px-6 py-3">
                                            <button onclick="deleteFolder(<?php echo $group->getId(); ?>)"
                                                class="inline-flex items-center justify-center rounded px-2 py-1 text-xs bg-red-50 text-red-600 hover:bg-red-100 transition">
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>

                                <!-- Files -->
                                <?php foreach ($filesInCurrentFolder as $upload): ?>
                                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                                        <td class="px-6 py-3">
                                            <div class="flex items-center">
                                                <i data-lucide="<?php echo getMimeIcon($upload->getMimetype()); ?>"
                                                    class="w-4 h-4 mr-2 text-gray-400"></i>
                                                <span><?php echo htmlspecialchars($upload->getSlug()); ?></span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-3"><?php echo htmlspecialchars($upload->getMimetype()); ?></td>
                                        <td class="px-6 py-3"><?php echo formatFileSize($upload->getSize()); ?></td>
                                        <td class="px-6 py-3">
                                            <div class="flex gap-2">
                                                <a href="../../<?php echo htmlspecialchars($upload->getRelativePath()); ?>"
                                                    target="_blank"
                                                    class="inline-flex items-center justify-center rounded px-2 py-1 text-xs bg-blue-50 text-blue-600 hover:bg-blue-100 transition">
                                                    View
                                                </a>
                                                <a href="../../<?php echo htmlspecialchars($upload->getRelativePath()); ?>"
                                                    download
                                                    class="inline-flex items-center justify-center rounded px-2 py-1 text-xs bg-green-50 text-green-600 hover:bg-green-100 transition">
                                                    Download
                                                </a>
                                                <button onclick="deleteFile(<?php echo $upload->getId(); ?>)"
                                                    class="inline-flex items-center justify-center rounded px-2 py-1 text-xs bg-red-50 text-red-600 hover:bg-red-100 transition">
                                                    Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>

                                <?php if (empty($childGroups) && empty($filesInCurrentFolder)): ?>
                                    <tr>
                                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                            <div class="flex flex-col items-center">
                                                <i data-lucide="inbox" class="w-12 h-12 mb-4 text-gray-400"></i>
                                                <p>No files or folders in this location</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

                <!-- Back Button -->
                <?php if ($currentFolderId !== 0): ?>
                    <div class="mt-6">
                        <a href="?view=<?php echo $viewMode; ?>"
                            class="inline-flex items-center gap-2 rounded-lg bg-gray-200 px-4 py-2 text-sm font-medium text-gray-900 transition hover:bg-gray-300">
                            <i data-lucide="arrow-left" class="w-4 h-4"></i>
                            Back to Root
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </section>

    <!-- Delete Forms (Hidden) -->
    <form id="deleteFileForm" method="POST" class="hidden">
        <input type="hidden" name="action" value="delete_file">
        <input type="hidden" name="file_id" id="fileId">
    </form>

    <form id="deleteFolderForm" method="POST" class="hidden">
        <input type="hidden" name="action" value="delete_folder">
        <input type="hidden" name="folder_id" id="folderId">
    </form>

    <script>
        lucide.createIcons();

        function switchView(mode) {
            const folder = new URLSearchParams(window.location.search).get('folder');
            const folderParam = folder ? '&folder=' + folder : '';
            window.location.href = '?view=' + mode + folderParam;
        }

        function deleteFile(id) {
            if (confirm('Are you sure you want to delete this file?')) {
                document.getElementById('fileId').value = id;
                document.getElementById('deleteFileForm').submit();
            }
        }

        function deleteFolder(id) {
            if (confirm('Are you sure you want to delete this folder? Files will be moved to parent folder.')) {
                document.getElementById('folderId').value = id;
                document.getElementById('deleteFolderForm').submit();
            }
        }
    </script>
</body>

</html>