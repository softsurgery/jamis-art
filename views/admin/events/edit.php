<?php
session_start();

require_once __DIR__ . '/../../../lib/authHelper.php';
require_once __DIR__ . '/../../../controllers/EventController.php';
require_once __DIR__ . '/../../../controllers/ArtTypeController.php';
require_once __DIR__ . '/../../../controllers/UploadController.php';
require_once __DIR__ . '/../../../constants/upload-group-map.php';

requireAdmin();

$eventController = new EventController();
$artTypeController = new ArtTypeController();

$error = '';
$success = false;
$event = null;

$eventUploadGroupId = getGroupIdByName("root");

$id = $_GET['id'] ?? null;

if (!$id) {
    $error = 'No event ID provided.';
} else {
    $event = $eventController->getById($id);
    if (!$event) {
        $error = 'Event not found.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $event) {
    try {
        $coverId = $event['uploadId'] ?? null;
        if (isset($_FILES['cover']) && $_FILES['cover']['error'] === UPLOAD_ERR_OK) {
            $mime = mime_content_type($_FILES['cover']['tmp_name']);
            if (strpos($mime, 'image/') === 0) {
                $upload = UploadController::uploadFile($_FILES['cover'], false, $eventUploadGroupId);
                $coverId = $upload->getId();
            }
        }

        $updatedEvent = new Event(
            $id,
            $_POST['title'],
            $coverId,
            $_POST['description'],
            $_POST['markdown'],
            $_POST['artTypeId'],
            $event['createdAt']
        );
        $eventController->update($updatedEvent);
        $success = true;
    } catch (Exception $e) {
        $error = 'Error updating event: ' . $e->getMessage();
    }
}

$artTypes = $artTypeController->getAll();

$coverPreviewUrl = ($event && !empty($event['coverPath'])) ? '../../../' . $event['coverPath'] : '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event</title>
    <link rel="stylesheet" href="https://unpkg.com/easymde/dist/easymde.min.css">
    <link rel="stylesheet" href="../../../assets/css/easymde-media.css">
    <script src="https://unpkg.com/easymde/dist/easymde.min.js"></script>
    <script src="../../../assets/js/easymde-media.js"></script>
    <script src="../../../assets/js/tailwind.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
</head>

<body class="flex flex-col flex-1 h-screen w-screen overflow-hidden">
    <?php if ($success): ?>
        <script>
            setTimeout(() => {
                window.location.href = '../events.php';
            }, 1500);
        </script>
    <?php endif; ?>

    <section class="flex flex-row flex-1 overflow-hidden">
        <div class="flex flex-col">
            <nav class="flex flex-col w-[15vw] h-full p-4">
                <?php
                require_once __DIR__ . "/../../../components/admin/nav.php";
                echo navLayout("events", "../../../");
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
                            <h1 class="text-2xl font-bold text-gray-900">Edit Event</h1>
                            <p class="mt-1 text-sm text-gray-500">Update the event details below.</p>
                        </div>
                        <a href="../events.php"
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
                            <a href="../events.php"
                                class="inline-flex items-center justify-center px-5 py-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                                Back to Events
                            </a>
                        </div>
                    <?php elseif ($success): ?>
                        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg flex gap-3">
                            <i data-lucide="check-circle" class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5"></i>
                            <div>
                                <h3 class="font-medium text-green-900">Success</h3>
                                <p class="text-sm text-green-700">Event updated successfully. Redirecting...</p>
                            </div>
                        </div>
                    <?php else: ?>
                        <form method="POST" enctype="multipart/form-data" class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Title <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="title" required placeholder="e.g. Annual Art Exhibition"
                                    value="<?= htmlspecialchars($event['title'] ?? '') ?>"
                                    class="w-full border border-gray-300 rounded-lg p-3 text-sm outline-none focus:ring-2 focus:ring-indigo-600/50 focus:border-indigo-600 transition">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Description <span
                                        class="text-red-500">*</span></label>
                                <textarea name="description" required placeholder="Brief description of the event"
                                    class="w-full border border-gray-300 rounded-lg p-3 text-sm outline-none focus:ring-2 focus:ring-indigo-600/50 focus:border-indigo-600 transition resize-none"
                                    rows="3"><?= htmlspecialchars($event['description'] ?? '') ?></textarea>
                                <p class="mt-1 text-xs text-gray-500">A short summary that will appear as a preview.</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Markdown Content (Planning) <span
                                        class="text-red-500">*</span></label>
                                <textarea id="markdown" name="markdown" placeholder="Detailed planning and content"
                                    class="w-full border border-gray-300 rounded-lg p-3 text-sm outline-none focus:ring-2 focus:ring-indigo-600/50 focus:border-indigo-600 transition resize-none"
                                    rows="8"><?= htmlspecialchars($event['markdown'] ?? '') ?></textarea>
                                <p class="mt-1 text-xs text-gray-500">The detailed markdown of the event.</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Art Type <span
                                        class="text-red-500">*</span></label>
                                <select name="artTypeId" required
                                    class="w-full border border-gray-300 rounded-lg p-3 text-sm outline-none focus:ring-2 focus:ring-indigo-600/50 focus:border-indigo-600 transition">
                                    <option value="">Select an art type</option>
                                    <?php foreach ($artTypes as $type): ?>
                                        <option value="<?= $type['id'] ?>" <?= $type['id'] == ($event['artTypeId'] ?? '') ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($type['label']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Cover Image</label>
                                <img id="cover-preview" src="<?= htmlspecialchars($coverPreviewUrl) ?>" alt="Cover preview"
                                    class="<?= $coverPreviewUrl ? '' : 'hidden' ?> mb-3 w-full max-w-xs aspect-video rounded-lg object-cover border border-gray-200 shadow-sm">
                                <input type="file" name="cover" id="cover" accept="image/*"
                                    class="w-full border border-gray-300 rounded-lg p-3 text-sm outline-none focus:ring-2 focus:ring-indigo-600/50 focus:border-indigo-600 transition">
                                <p class="mt-1 text-xs text-gray-500">Optional thumbnail shown in collection listings. Leave
                                    empty to keep the current cover.</p>
                            </div>

                            <div class="pt-6 border-t border-gray-200 flex gap-3">
                                <a href="../events.php"
                                    class="flex-1 inline-flex items-center justify-center px-5 py-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition focus:ring-4 focus:ring-gray-200">
                                    Cancel
                                </a>
                                <button type="submit"
                                    class="flex-1 inline-flex items-center justify-center px-5 py-3 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition focus:ring-4 focus:ring-indigo-600/20">
                                    Update Event
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

        initEasyMDEWithMedia({
            element: document.getElementById('markdown'),
            apiUrl: '../api/media.php',
            previewBasePath: '../../../',
        });

        document.getElementById('cover').addEventListener('change', (e) => {
            const preview = document.getElementById('cover-preview');
            const file = e.target.files[0];
            if (file) {
                preview.src = URL.createObjectURL(file);
                preview.classList.remove('hidden');
            }
        });
    </script>
</body>

</html>
