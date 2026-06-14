<?php
session_start();
require_once __DIR__ . '/../../lib/authHelper.php';
require_once __DIR__ . '/../../controllers/UploadController.php';

requireAdmin();

$currentFolder = isset($_GET['folder']) ? $_GET['folder'] : '';
$viewMode = isset($_GET['view']) ? $_GET['view'] : 'grid'; // grid or list

// Get all files
$allUploads = UploadController::findAll();

// Organize files by folder
$folders = [];
$filesInCurrentFolder = [];

foreach ($allUploads as $upload) {
    $path = $upload->getRelativePath();
    // Extract folder from path (e.g., "storage/folder/file.jpg" -> "folder")
    $pathParts = explode('/', $path);

    if (count($pathParts) > 2) {
        $folder = $pathParts[1];
    } else {
        $folder = '';
    }

    // Add to folders list
    if ($folder && !in_array($folder, $folders)) {
        $folders[] = $folder;
    }

    // Add to current folder files
    if ($folder === $currentFolder) {
        $filesInCurrentFolder[] = $upload;
    } elseif (!$folder && !$currentFolder) {
        $filesInCurrentFolder[] = $upload;
    }
}

sort($folders);

// Handle delete action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'delete' && isset($_POST['id'])) {
        UploadController::delete($_POST['id']);
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Helper function to get file icon based on MIME type
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

    <style>
        .file-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 1rem;
        }

        .file-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            overflow: hidden;
            transition: all 0.2s;
            cursor: pointer;
        }

        .file-card:hover {
            border-color: #4f46e5;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .file-thumbnail {
            width: 100%;
            height: 120px;
            background: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            font-size: 2rem;
            color: #9ca3af;
        }

        .file-thumbnail img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .folder-icon {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
        }

        .file-info {
            padding: 0.5rem;
            text-align: center;
        }

        .file-name {
            font-size: 0.75rem;
            font-weight: 500;
            color: #1f2937;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            margin-bottom: 0.25rem;
        }

        .file-size {
            font-size: 0.7rem;
            color: #9ca3af;
        }

        .file-actions {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
            padding: 0.5rem;
            border-top: 1px solid #f3f4f6;
            opacity: 0;
            transition: opacity 0.2s;
        }

        .file-card:hover .file-actions {
            opacity: 1;
        }

        .file-action-btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            border: none;
            border-radius: 0.25rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .breadcrumb-item {
            display: inline-flex;
            align-items: center;
            margin-right: 0.5rem;
        }

        .breadcrumb-item a {
            color: #4f46e5;
            text-decoration: none;
        }

        .breadcrumb-item a:hover {
            text-decoration: underline;
        }
    </style>
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
                        <?php if ($currentFolder): ?>
                            <span class="text-gray-400 mx-2">/</span>
                            <span class="text-gray-900 font-medium"><?php echo htmlspecialchars($currentFolder); ?></span>
                        <?php endif; ?>
                    </span>
                </div>

                <!-- Main Content -->
                <?php if ($viewMode === 'grid'): ?>
                    <!-- GRID VIEW -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6">
                        <div class="file-grid">
                            <!-- Folders -->
                            <?php foreach ($folders as $folder): ?>
                                <div class="file-card">
                                    <a href="?folder=<?php echo urlencode($folder); ?>&view=grid">
                                        <div class="file-thumbnail">
                                            <div class="folder-icon">
                                                <i data-lucide="folder" style="width: 48px; height: 48px; color: white;"></i>
                                            </div>
                                        </div>
                                        <div class="file-info">
                                            <div class="file-name"><?php echo htmlspecialchars($folder); ?></div>
                                            <div class="file-size">Folder</div>
                                        </div>
                                    </a>
                                </div>
                            <?php endforeach; ?>

                            <!-- Files -->
                            <?php foreach ($filesInCurrentFolder as $upload): ?>
                                <div class="file-card">
                                    <div class="file-thumbnail">
                                        <?php
                                        $thumbnail = getThumbnailUrl($upload);
                                        if ($thumbnail):
                                            ?>
                                            <img src="../../<?php echo htmlspecialchars($thumbnail); ?>"
                                                alt="<?php echo htmlspecialchars($upload->getSlug()); ?>">
                                        <?php else: ?>
                                            <i data-lucide="<?php echo getMimeIcon($upload->getMimetype()); ?>"
                                                style="width: 48px; height: 48px;"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="file-info">
                                        <div class="file-name" title="<?php echo htmlspecialchars($upload->getSlug()); ?>">
                                            <?php echo htmlspecialchars($upload->getSlug()); ?></div>
                                        <div class="file-size"><?php echo formatFileSize($upload->getSize()); ?></div>
                                    </div>
                                    <div class="file-actions">
                                        <a href="../../<?php echo htmlspecialchars($upload->getRelativePath()); ?>"
                                            target="_blank" class="file-action-btn bg-blue-500 text-white hover:bg-blue-600">
                                            <i data-lucide="eye" class="w-3 h-3"></i>
                                        </a>
                                        <a href="../../<?php echo htmlspecialchars($upload->getRelativePath()); ?>" download
                                            class="file-action-btn bg-green-500 text-white hover:bg-green-600">
                                            <i data-lucide="download" class="w-3 h-3"></i>
                                        </a>
                                        <button onclick="deleteFile(<?php echo $upload->getId(); ?>)"
                                            class="file-action-btn bg-red-500 text-white hover:bg-red-600">
                                            <i data-lucide="trash-2" class="w-3 h-3"></i>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                            <?php if (empty($folders) && empty($filesInCurrentFolder)): ?>
                                <div class="col-span-full text-center py-12">
                                    <i data-lucide="inbox" class="w-12 h-12 mx-auto text-gray-400 mb-4"></i>
                                    <p class="text-gray-500">No files in this folder</p>
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
                                <?php foreach ($folders as $folder): ?>
                                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                                        <td class="px-6 py-3">
                                            <a href="?folder=<?php echo urlencode($folder); ?>&view=list"
                                                class="flex items-center text-indigo-600 hover:underline">
                                                <i data-lucide="folder" class="w-4 h-4 mr-2"></i>
                                                <span><?php echo htmlspecialchars($folder); ?></span>
                                            </a>
                                        </td>
                                        <td class="px-6 py-3">Folder</td>
                                        <td class="px-6 py-3">-</td>
                                        <td class="px-6 py-3">-</td>
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

                                <?php if (empty($folders) && empty($filesInCurrentFolder)): ?>
                                    <tr>
                                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                            <div class="flex flex-col items-center">
                                                <i data-lucide="inbox" class="w-12 h-12 mb-4 text-gray-400"></i>
                                                <p>No files in this folder</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

                <!-- Back Button -->
                <?php if ($currentFolder): ?>
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

    <!-- Delete Form (Hidden) -->
    <form id="deleteForm" method="POST" style="display: none;">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" id="deleteId">
    </form>

    <script>
        lucide.createIcons();

        function switchView(mode) {
            const folder = new URLSearchParams(window.location.search).get('folder');
            const folderParam = folder ? '&folder=' + encodeURIComponent(folder) : '';
            window.location.href = '?view=' + mode + folderParam;
        }

        function deleteFile(id) {
            if (confirm('Are you sure you want to delete this file?')) {
                document.getElementById('deleteId').value = id;
                document.getElementById('deleteForm').submit();
            }
        }
    </script>
</body>

</html>