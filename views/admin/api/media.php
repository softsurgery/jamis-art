<?php
session_start();

require_once __DIR__ . '/../../../lib/authHelper.php';
require_once __DIR__ . '/../../../controllers/UploadGroupController.php';

requireAdmin();

header('Content-Type: application/json');

$folderId = isset($_GET['folder']) ? (int) $_GET['folder'] : 0;

if ($folderId !== 0) {
    $currentGroup = UploadGroupController::getById($folderId);
    if (!$currentGroup) {
        http_response_code(404);
        echo json_encode(['error' => 'Folder not found']);
        exit;
    }
    $parentFolderId = (int) $currentGroup->getParent();
} else {
    $parentFolderId = 0;
}

global $pdo;

if ($folderId === 0) {
    $stmt = $pdo->prepare("SELECT * FROM upload WHERE groupeId IS NULL ORDER BY slug ASC");
    $stmt->execute();
} else {
    $stmt = $pdo->prepare("SELECT * FROM upload WHERE groupeId = :groupId ORDER BY slug ASC");
    $stmt->bindValue(':groupId', $folderId, PDO::PARAM_INT);
    $stmt->execute();
}

$images = [];
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    if (strpos($row['mimeType'], 'image/') !== 0) {
        continue;
    }

    $images[] = [
        'id' => (int) $row['id'],
        'slug' => $row['slug'],
        'path' => $row['relativePath'],
        'mimeType' => $row['mimeType'],
    ];
}

$folders = [];
foreach (UploadGroupController::getByParent($folderId) as $group) {
    $folders[] = [
        'id' => $group->getId(),
        'name' => $group->getName(),
    ];
}

$breadcrumbs = [['id' => 0, 'name' => 'Root']];
if ($folderId !== 0) {
    foreach (UploadGroupController::getBreadcrumbPath($folderId) as $crumb) {
        $breadcrumbs[] = [
            'id' => $crumb->getId(),
            'name' => $crumb->getName(),
        ];
    }
}

echo json_encode([
    'folderId' => $folderId,
    'parentFolderId' => $parentFolderId,
    'breadcrumbs' => $breadcrumbs,
    'folders' => $folders,
    'images' => $images,
]);
