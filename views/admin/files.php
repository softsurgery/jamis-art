<?php
session_start();
require_once __DIR__ . '/../../lib/authHelper.php';
require_once __DIR__ . '/../../controllers/UploadController.php';
require_once __DIR__ . '/../../controllers/UploadGroupController.php';

requireAdmin();

$currentFolderId = isset($_GET['folder']) ? (int) $_GET['folder'] : 0;
$viewMode = isset($_GET['view']) && $_GET['view'] === 'list' ? 'list' : 'grid';
$error = null;
$success = isset($_GET['uploaded']) ? 'Files uploaded successfully.' : null;

$currentGroup = null;
if ($currentFolderId !== 0) {
    $currentGroup = UploadGroupController::getById($currentFolderId);
    if (!$currentGroup) {
        $currentFolderId = 0;
    }
}

$parentFolderId = $currentGroup ? (int) $currentGroup->getParent() : 0;
$childGroups = UploadGroupController::getByParent($currentFolderId);

global $pdo;
$filesInCurrentFolder = [];
if ($currentFolderId === 0) {
    $stmt = $pdo->prepare("SELECT * FROM upload WHERE groupeId IS NULL ORDER BY slug ASC");
    $stmt->execute();
} else {
    $stmt = $pdo->prepare("SELECT * FROM upload WHERE groupeId = :groupId ORDER BY slug ASC");
    $stmt->bindValue(':groupId', $currentFolderId, PDO::PARAM_INT);
    $stmt->execute();
}
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($results as $row) {
    $filesInCurrentFolder[] = new Upload(
        $row['id'],
        $row['slug'],
        $row['relativePath'],
        $row['mimeType'],
        $row['size'],
        $row['isTemporary'],
        $row['isPrivate'],
        $row['groupeId']
    );
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $redirectUrl = $_SERVER['PHP_SELF'] . "?folder=$currentFolderId&view=$viewMode";

    if ($_POST['action'] === 'delete_file' && isset($_POST['file_id'])) {
        UploadController::delete($_POST['file_id']);
        header("Location: $redirectUrl");
        exit;
    } elseif ($_POST['action'] === 'create_folder' && isset($_POST['folder_name'])) {
        try {
            UploadGroupController::create($_POST['folder_name'], $currentFolderId);
            header("Location: $redirectUrl");
            exit;
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    } elseif ($_POST['action'] === 'delete_folder' && isset($_POST['folder_id'])) {
        try {
            UploadGroupController::delete($_POST['folder_id']);
            header("Location: $redirectUrl");
            exit;
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    } elseif ($_POST['action'] === 'upload_files' && isset($_FILES['files'])) {
        try {
            $groupeId = $currentFolderId === 0 ? null : $currentFolderId;
            UploadController::uploadMultipleFiles($_FILES['files'], $groupeId);
            header("Location: $redirectUrl&uploaded=1");
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
        return 'file-type';
    if (strpos($mimeType, 'text/') === 0)
        return 'file-text';
    if (strpos($mimeType, 'spreadsheet') !== false || strpos($mimeType, 'excel') !== false)
        return 'file-spreadsheet';
    return 'file';
}

function formatFileSize($bytes)
{
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= (1 << (10 * $pow));
    return round($bytes, 2) . ' ' . $units[$pow];
}

function getThumbnailUrl($upload)
{
    if (strpos($upload->getMimetype(), 'image/') === 0) {
        return $upload->getRelativePath();
    }
    return null;
}

function buildFolderUrl($folderId, $viewMode)
{
    $params = ['view' => $viewMode];
    if ($folderId !== 0) {
        $params['folder'] = $folderId;
    }
    return '?' . http_build_query($params);
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
                <div class="flex items-center justify-between pb-4 border-b border-gray-200 mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">File Management</h1>
                        <p class="mt-1 text-sm text-gray-500">Upload, organize, and manage files by folder.</p>
                    </div>
                    <div class="flex gap-2">
                        <button onclick="switchView('grid')"
                            class="inline-flex items-center justify-center rounded-lg <?php echo $viewMode === 'grid' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-600 border border-gray-200'; ?> px-4 py-2 text-sm font-medium transition hover:bg-indigo-700">
                            <i data-lucide="layout-grid" class="w-4 h-4 mr-2"></i>
                            Grid
                        </button>
                        <button onclick="switchView('list')"
                            class="inline-flex items-center justify-center rounded-lg <?php echo $viewMode === 'list' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-600 border border-gray-200'; ?> px-4 py-2 text-sm font-medium transition hover:bg-indigo-700">
                            <i data-lucide="list" class="w-4 h-4 mr-2"></i>
                            List
                        </button>
                    </div>
                </div>

                <?php if ($error): ?>
                    <div class="mb-4 flex items-center gap-2 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                        <i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i>
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="mb-4 flex items-center gap-2 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                        <i data-lucide="check-circle" class="w-4 h-4 shrink-0"></i>
                        <?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>

                <div class="mb-6 p-3 bg-white rounded-lg border border-gray-200 flex items-center gap-2 text-sm text-gray-600">
                    <i data-lucide="folder-open" class="w-4 h-4 text-indigo-500 shrink-0"></i>
                    <a href="<?php echo buildFolderUrl(0, $viewMode); ?>"
                        class="inline-flex items-center gap-1 text-indigo-600 hover:underline">
                        <i data-lucide="home" class="w-3.5 h-3.5"></i>
                        Root
                    </a>
                    <?php
                    if ($currentFolderId !== 0 && $currentGroup) {
                        $breadcrumbs = UploadGroupController::getBreadcrumbPath($currentFolderId);
                        foreach ($breadcrumbs as $crumb) {
                            echo '<i data-lucide="chevron-right" class="w-3.5 h-3.5 text-gray-400"></i>';
                            echo '<a href="' . buildFolderUrl($crumb->getId(), $viewMode) . '" class="text-indigo-600 hover:underline">' . htmlspecialchars($crumb->getName()) . '</a>';
                        }
                    }
                    ?>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-[1fr_auto] gap-4 items-start mb-6">
                    <form method="POST" enctype="multipart/form-data" id="uploadForm"
                        class="bg-white rounded-lg border border-gray-200 p-4">
                        <input type="hidden" name="action" value="upload_files">
                        <div id="uploadZone" onclick="document.getElementById('fileInput').click()"
                            class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center bg-gray-50 transition cursor-pointer hover:border-indigo-500 hover:bg-indigo-50">
                            <i data-lucide="cloud-upload" class="w-10 h-10 mx-auto text-indigo-400 mb-2"></i>
                            <p class="text-sm font-medium text-gray-700">Drop files here or click to browse</p>
                            <p class="text-xs text-gray-400 mt-1">Multiple files supported</p>
                            <input type="file" name="files[]" id="fileInput" multiple class="hidden">
                            <div id="selectedFiles" class="hidden mt-3 text-xs text-gray-600 text-left"></div>
                        </div>
                        <button type="submit" id="uploadBtn" disabled
                            class="mt-3 w-full inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed">
                            <i data-lucide="upload" class="w-4 h-4 mr-2"></i>
                            Upload Files
                        </button>
                    </form>

                    <form method="POST"
                        class="bg-white rounded-lg border border-gray-200 p-4 flex flex-col gap-3 min-w-[240px]">
                        <input type="hidden" name="action" value="create_folder">
                        <div class="flex items-center gap-2 text-sm font-medium text-gray-700">
                            <i data-lucide="folder-plus" class="w-4 h-4 text-amber-500"></i>
                            New Folder
                        </div>
                        <input type="text" name="folder_name" placeholder="Folder name..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            required>
                        <button type="submit"
                            class="inline-flex items-center justify-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-emerald-700">
                            <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                            Create
                        </button>
                    </form>
                </div>

                <?php if ($viewMode === 'grid'): ?>
                    <div class="bg-white rounded-lg border border-gray-200 p-6">
                        <div class="grid grid-cols-[repeat(auto-fill,minmax(160px,1fr))] gap-4">
                            <?php foreach ($childGroups as $group): ?>
                                <div
                                    class="group flex flex-col bg-white border border-gray-200 rounded-xl overflow-hidden transition hover:border-indigo-500 hover:shadow-md hover:shadow-indigo-500/10">
                                    <a href="<?php echo buildFolderUrl($group->getId(), 'grid'); ?>" class="block">
                                        <div
                                            class="w-full h-[120px] bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center overflow-hidden">
                                            <i data-lucide="folder" class="w-12 h-12 text-white"></i>
                                        </div>
                                        <div class="px-3 py-2.5 text-center flex-1">
                                            <div class="text-xs font-medium text-gray-800 truncate"
                                                title="<?php echo htmlspecialchars($group->getName()); ?>">
                                                <?php echo htmlspecialchars($group->getName()); ?>
                                            </div>
                                            <div class="text-[0.7rem] text-gray-400 mt-0.5">Folder</div>
                                        </div>
                                    </a>
                                    <div
                                        class="flex gap-1.5 p-2 border-t border-gray-100 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button type="button" onclick="deleteFolder(<?php echo $group->getId(); ?>)"
                                            class="flex-1 inline-flex items-center justify-center p-1.5 rounded-md bg-red-500 text-white hover:bg-red-600 transition border-none cursor-pointer"
                                            title="Delete folder">
                                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                            <?php foreach ($filesInCurrentFolder as $upload): ?>
                                <?php $thumbnail = getThumbnailUrl($upload); ?>
                                <div
                                    class="group flex flex-col bg-white border border-gray-200 rounded-xl overflow-hidden transition hover:border-indigo-500 hover:shadow-md hover:shadow-indigo-500/10">
                                    <div
                                        class="w-full h-[120px] bg-gray-100 flex items-center justify-center overflow-hidden">
                                        <?php if ($thumbnail): ?>
                                            <img src="../../<?php echo htmlspecialchars($thumbnail); ?>"
                                                alt="<?php echo htmlspecialchars($upload->getSlug()); ?>"
                                                class="w-full h-full object-cover">
                                        <?php else: ?>
                                            <i data-lucide="<?php echo getMimeIcon($upload->getMimetype()); ?>"
                                                class="w-12 h-12 text-gray-400"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="px-3 py-2.5 text-center flex-1">
                                        <div class="text-xs font-medium text-gray-800 truncate"
                                            title="<?php echo htmlspecialchars($upload->getSlug()); ?>">
                                            <?php echo htmlspecialchars($upload->getSlug()); ?>
                                        </div>
                                        <div class="text-[0.7rem] text-gray-400 mt-0.5">
                                            <?php echo formatFileSize($upload->getSize()); ?>
                                        </div>
                                    </div>
                                    <div
                                        class="flex gap-1.5 p-2 border-t border-gray-100 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <a href="../../<?php echo htmlspecialchars($upload->getRelativePath()); ?>"
                                            target="_blank"
                                            class="flex-1 inline-flex items-center justify-center p-1.5 rounded-md bg-blue-500 text-white hover:bg-blue-600 transition no-underline"
                                            title="View">
                                            <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                        </a>
                                        <a href="../../<?php echo htmlspecialchars($upload->getRelativePath()); ?>"
                                            download
                                            class="flex-1 inline-flex items-center justify-center p-1.5 rounded-md bg-emerald-500 text-white hover:bg-emerald-600 transition no-underline"
                                            title="Download">
                                            <i data-lucide="download" class="w-3.5 h-3.5"></i>
                                        </a>
                                        <button type="button" onclick="deleteFile(<?php echo $upload->getId(); ?>)"
                                            class="flex-1 inline-flex items-center justify-center p-1.5 rounded-md bg-red-500 text-white hover:bg-red-600 transition border-none cursor-pointer"
                                            title="Delete">
                                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                            <?php if (empty($childGroups) && empty($filesInCurrentFolder)): ?>
                                <div class="col-span-full text-center py-12">
                                    <i data-lucide="inbox" class="w-12 h-12 mx-auto text-gray-300 mb-4"></i>
                                    <p class="text-gray-500 text-sm">No files or folders here yet</p>
                                    <p class="text-gray-400 text-xs mt-1">Upload files or create a folder to get started</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                <?php else: ?>
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
                        <table class="w-full text-left text-sm text-gray-600">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3 font-semibold text-gray-900">Name</th>
                                    <th class="px-6 py-3 font-semibold text-gray-900">Type</th>
                                    <th class="px-6 py-3 font-semibold text-gray-900">Size</th>
                                    <th class="px-6 py-3 font-semibold text-gray-900 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($childGroups as $group): ?>
                                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                                        <td class="px-6 py-3">
                                            <a href="<?php echo buildFolderUrl($group->getId(), 'list'); ?>"
                                                class="flex items-center text-indigo-600 hover:underline font-medium">
                                                <i data-lucide="folder" class="w-4 h-4 mr-2 text-amber-500"></i>
                                                <span><?php echo htmlspecialchars($group->getName()); ?></span>
                                            </a>
                                        </td>
                                        <td class="px-6 py-3">Folder</td>
                                        <td class="px-6 py-3">—</td>
                                        <td class="px-6 py-3 text-right">
                                            <button onclick="deleteFolder(<?php echo $group->getId(); ?>)"
                                                class="inline-flex items-center gap-1 rounded px-2.5 py-1 text-xs bg-red-50 text-red-600 hover:bg-red-100 transition">
                                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>

                                <?php foreach ($filesInCurrentFolder as $upload): ?>
                                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                                        <td class="px-6 py-3">
                                            <div class="flex items-center font-medium text-gray-900">
                                                <i data-lucide="<?php echo getMimeIcon($upload->getMimetype()); ?>"
                                                    class="w-4 h-4 mr-2 text-gray-400"></i>
                                                <span><?php echo htmlspecialchars($upload->getSlug()); ?></span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-3"><?php echo htmlspecialchars($upload->getMimetype()); ?></td>
                                        <td class="px-6 py-3"><?php echo formatFileSize($upload->getSize()); ?></td>
                                        <td class="px-6 py-3">
                                            <div class="flex gap-2 justify-end">
                                                <a href="../../<?php echo htmlspecialchars($upload->getRelativePath()); ?>"
                                                    target="_blank"
                                                    class="inline-flex items-center gap-1 rounded px-2.5 py-1 text-xs bg-blue-50 text-blue-600 hover:bg-blue-100 transition">
                                                    <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                                    View
                                                </a>
                                                <a href="../../<?php echo htmlspecialchars($upload->getRelativePath()); ?>"
                                                    download
                                                    class="inline-flex items-center gap-1 rounded px-2.5 py-1 text-xs bg-emerald-50 text-emerald-600 hover:bg-emerald-100 transition">
                                                    <i data-lucide="download" class="w-3.5 h-3.5"></i>
                                                    Download
                                                </a>
                                                <button onclick="deleteFile(<?php echo $upload->getId(); ?>)"
                                                    class="inline-flex items-center gap-1 rounded px-2.5 py-1 text-xs bg-red-50 text-red-600 hover:bg-red-100 transition">
                                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
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
                                                <i data-lucide="inbox" class="w-12 h-12 mb-4 text-gray-300"></i>
                                                <p>No files or folders here yet</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

                <?php if ($currentFolderId !== 0): ?>
                    <div class="mt-6">
                        <a href="<?php echo buildFolderUrl($parentFolderId, $viewMode); ?>"
                            class="inline-flex items-center gap-2 rounded-lg bg-white border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-100">
                            <i data-lucide="arrow-left" class="w-4 h-4"></i>
                            <?php echo $parentFolderId === 0 ? 'Back to Root' : 'Back'; ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </section>

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
            const params = new URLSearchParams(window.location.search);
            params.set('view', mode);
            window.location.search = params.toString();
        }

        function deleteFile(id) {
            if (confirm('Are you sure you want to delete this file?')) {
                document.getElementById('fileId').value = id;
                document.getElementById('deleteFileForm').submit();
            }
        }

        function deleteFolder(id) {
            if (confirm('Are you sure you want to delete this folder? Files will be moved to the parent folder.')) {
                document.getElementById('folderId').value = id;
                document.getElementById('deleteFolderForm').submit();
            }
        }

        const uploadZone = document.getElementById('uploadZone');
        const fileInput = document.getElementById('fileInput');
        const selectedFiles = document.getElementById('selectedFiles');
        const uploadBtn = document.getElementById('uploadBtn');

        const dragActive = ['border-indigo-500', 'bg-indigo-50'];
        const dragInactive = ['border-gray-300', 'bg-gray-50'];

        function setDragState(active) {
            if (active) {
                uploadZone.classList.remove(...dragInactive);
                uploadZone.classList.add(...dragActive);
            } else {
                uploadZone.classList.remove(...dragActive);
                uploadZone.classList.add(...dragInactive);
            }
        }

        function updateSelectedFiles() {
            const files = fileInput.files;
            if (files.length === 0) {
                selectedFiles.classList.add('hidden');
                selectedFiles.innerHTML = '';
                uploadBtn.disabled = true;
                return;
            }
            uploadBtn.disabled = false;
            selectedFiles.classList.remove('hidden');
            selectedFiles.innerHTML = '<strong>Selected:</strong> ' +
                Array.from(files).map(f => f.name).join(', ');
        }

        fileInput.addEventListener('change', updateSelectedFiles);

        uploadZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            setDragState(true);
        });

        uploadZone.addEventListener('dragleave', () => setDragState(false));

        uploadZone.addEventListener('drop', (e) => {
            e.preventDefault();
            setDragState(false);
            fileInput.files = e.dataTransfer.files;
            updateSelectedFiles();
        });
    </script>
</body>

</html>
