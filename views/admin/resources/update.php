<?php
session_start();

require_once __DIR__ . '/../../../lib/authHelper.php';
require_once __DIR__ . '/../../../controllers/ResourceController.php';
require_once __DIR__ . '/../../../controllers/ArtTypeController.php';
require_once __DIR__ . '/../../../controllers/UploadController.php';
require_once __DIR__ . '/../../../constants/upload-group-map.php';

requireAdmin();

$resourceController = new ResourceController();
$artTypeController = new ArtTypeController();

$error = '';
$success = false;
$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: ../resources.php");
    exit;
}

$resource = $resourceController->getById($id);
if (!$resource) {
    header("Location: ../resources.php");
    exit;
}

$resourceUploadGroupId = getGroupIdByName("resources");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $uploadId = $resource->getUploadId();
        
        // Check if a new file was uploaded to replace the old one
        if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
            $upload = UploadController::uploadFile($_FILES['file'], false, $resourceUploadGroupId);
            $uploadId = $upload->getId();
            
            // Optionally we could delete the old file here, 
            // but we'll leave it in the database/storage to be safe or clean it up via files management
            // UploadController::delete($resource->getUploadId());
        }

        $resource->setLabel($_POST['label']);
        $resource->setDescription($_POST['description']);
        $resource->setArtTypeId($_POST['artTypeId']);
        $resource->setUploadId($uploadId);

        $resourceController->update($id, $resource);
        
        $success = true;
    } catch (Exception $e) {
        $error = 'Error updating resource: ' . $e->getMessage();
    }
}

$artTypes = $artTypeController->getAll();
$currentUpload = UploadController::getFileByIdOrSlug($resource->getUploadId());
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Resource</title>
    <script src="../../../assets/js/tailwind.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
</head>

<body class="flex flex-col flex-1 h-screen w-screen overflow-hidden font-poppins">
    <?php if ($success): ?>
        <script>
            setTimeout(() => {
                window.location.href = '../resources.php?type=<?= urlencode($resource->getArtTypeId()) ?>';
            }, 1000);
        </script>
    <?php endif; ?>

    <section class="flex flex-row flex-1 overflow-hidden">
        <div class="flex flex-col">
            <nav class="flex flex-col w-[15vw] h-full p-4">
                <?php
                require_once __DIR__ . "/../../../components/admin/nav.php";
                echo navLayout("resources", "../../../");
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
                <div class="container mx-auto rounded-lg shadow-sm p-8 max-w-3xl">
                    <div class="flex items-center justify-between pb-6 border-b border-gray-200 mb-6">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Update Resource</h1>
                            <p class="mt-1 text-sm text-gray-500">Edit the details of this resource.</p>
                        </div>
                        <a href="../resources.php?type=<?= urlencode($resource->getArtTypeId()) ?>"
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
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg flex gap-3">
                            <i data-lucide="check-circle" class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5"></i>
                            <div>
                                <h3 class="font-medium text-green-900">Success</h3>
                                <p class="text-sm text-green-700">Resource updated successfully. Redirecting...</p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!$error && !$success): ?>
                        <form method="POST" enctype="multipart/form-data" class="space-y-6">
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Art Type <span class="text-red-500">*</span></label>
                                <select name="artTypeId" required
                                    class="w-full border border-gray-300 rounded-lg p-3 text-sm outline-none focus:ring-2 focus:ring-indigo-600/50 focus:border-indigo-600 transition bg-gray-50 focus:bg-white">
                                    <option value="">Select an art type</option>
                                    <?php foreach ($artTypes as $type): ?>
                                        <option value="<?= $type['id'] ?>" <?= $resource->getArtTypeId() == $type['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($type['label']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Label <span class="text-red-500">*</span></label>
                                <input type="text" name="label" required value="<?= htmlspecialchars($resource->getLabel()) ?>"
                                    class="w-full border border-gray-300 rounded-lg p-3 text-sm outline-none focus:ring-2 focus:ring-indigo-600/50 focus:border-indigo-600 transition bg-gray-50 focus:bg-white">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                <textarea name="description" placeholder="Brief description of the resource"
                                    class="w-full border border-gray-300 rounded-lg p-3 text-sm outline-none focus:ring-2 focus:ring-indigo-600/50 focus:border-indigo-600 transition resize-none bg-gray-50 focus:bg-white"
                                    rows="4"><?= htmlspecialchars($resource->getDescription()) ?></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">File (Optional)</label>
                                <?php if ($currentUpload): ?>
                                    <div class="mb-3 p-3 bg-gray-50 border border-gray-200 rounded-lg flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <i data-lucide="file" class="w-5 h-5 text-gray-500"></i>
                                            <span class="text-sm font-medium text-gray-700"><?= htmlspecialchars(basename($currentUpload->getRelativePath())) ?></span>
                                        </div>
                                        <a href="../../../<?= htmlspecialchars($currentUpload->getRelativePath()) ?>" target="_blank" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View Current</a>
                                    </div>
                                <?php endif; ?>
                                <input type="file" name="file"
                                    class="w-full border border-gray-300 rounded-lg p-2 text-sm outline-none focus:ring-2 focus:ring-indigo-600/50 focus:border-indigo-600 transition bg-gray-50 focus:bg-white file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                <p class="mt-1 text-xs text-gray-500">Upload a new file only if you want to replace the current one.</p>
                            </div>

                            <div class="pt-6 border-t border-gray-200 flex gap-3">
                                <a href="../resources.php?type=<?= urlencode($resource->getArtTypeId()) ?>"
                                    class="flex-1 inline-flex items-center justify-center px-5 py-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition focus:ring-4 focus:ring-gray-200">
                                    Cancel
                                </a>
                                <button type="submit"
                                    class="flex-1 inline-flex items-center justify-center px-5 py-3 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition focus:ring-4 focus:ring-indigo-600/20 shadow-sm">
                                    Update Resource
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
