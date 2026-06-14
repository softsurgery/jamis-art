<?php
require_once __DIR__ . '/../connect.php';
require_once __DIR__ . '/../models/Upload.php';

class UploadController
{
    /**
     * Helper to generate a unique slug for files.
     */
    private static function generateSlug($filename)
    {
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        return uniqid() . '-' . time() . '.' . $ext;
    }

    /**
     * Gets storage directory or creates it if it doesn't exist.
     */
    private static function getStorageDir()
    {
        $dir = __DIR__ . '/../storage';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        return $dir;
    }

    /**
     * Get /list endpoint logic
     */
    public static function findAllPaginated($limit = 10, $offset = 0)
    {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM upload LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $uploads = [];
        foreach ($results as $row) {
            $uploads[] = new Upload($row['id'], $row['slug'], $row['relativePath'], $row['mimeType'], $row['size'], $row['isTemporary'], $row['isPrivate'], $row['groupeId']);
        }

        $stmtCount = $pdo->query("SELECT COUNT(*) FROM upload");
        $total = $stmtCount->fetchColumn();

        return [
            'data' => $uploads,
            'total' => $total,
            'limit' => $limit,
            'offset' => $offset
        ];
    }

    /**
     * Get /all endpoint logic
     */
    public static function findAll()
    {
        global $pdo;
        $stmt = $pdo->query("SELECT * FROM upload");
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $uploads = [];
        foreach ($results as $row) {
            $uploads[] = new Upload($row['id'], $row['slug'], $row['relativePath'], $row['mimeType'], $row['size'], $row['isTemporary'], $row['isPrivate'], $row['groupeId']);
        }
        return $uploads;
    }

    /**
     * Get :id or slug endpoint logic
     */
    public static function getFileByIdOrSlug($identifier)
    {
        global $pdo;
        if (is_numeric($identifier)) {
            $stmt = $pdo->prepare("SELECT * FROM upload WHERE id = :id");
            $stmt->execute(['id' => $identifier]);
        } else {
            $stmt = $pdo->prepare("SELECT * FROM upload WHERE slug = :slug");
            $stmt->execute(['slug' => $identifier]);
        }

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new Upload($row['id'], $row['slug'], $row['relativePath'], $row['mimeType'], $row['size'], $row['isTemporary'], $row['isPrivate'], $row['groupeId']);
        }
        return null;
    }

    /**
     * Post /upload endpoint logic
     */
    public static function uploadFile($fileData, $isTemporary = false, $groupeId = null)
    {
        if (!isset($fileData['tmp_name']) || $fileData['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("File upload error.");
        }

        global $pdo;
        $slug = self::generateSlug($fileData['name']);

        $storageDir = self::getStorageDir();
        $relativePath = 'storage/' . $slug;
        $absolutePath = $storageDir . '/' . $slug;

        if (move_uploaded_file($fileData['tmp_name'], $absolutePath)) {
            $mimeType = file_exists($absolutePath) && function_exists('mime_content_type')
                ? mime_content_type($absolutePath)
                : $fileData['type'];
            $size = filesize($absolutePath);

            $stmt = $pdo->prepare("INSERT INTO upload (slug, relativePath, mimeType, size, isTemporary, isPrivate, groupeId) VALUES (:slug, :relativePath, :mimeType, :size, :isTemporary, :isPrivate, :groupeId)");

            $stmt->execute([
                'slug' => $slug,
                'relativePath' => $relativePath,
                'mimeType' => $mimeType,
                'size' => $size,
                'isTemporary' => $isTemporary ? 1 : 0,
                'isPrivate' => 0,
                'groupeId' => $groupeId
            ]);

            $id = $pdo->lastInsertId();
            return new Upload($id, $slug, $relativePath, $mimeType, $size, $isTemporary, false, $groupeId);
        }

        throw new Exception("Failed to move uploaded file.");
    }

    /**
     * Post /upload/temporary endpoint logic
     */
    public static function uploadTemporaryFile($fileData)
    {
        return self::uploadFile($fileData, true);
    }

    /**
     * Helper to format multiple files array uploaded via PHP $_FILES
     */
    private static function formatFileArray($fileArrays)
    {
        $files = [];
        if (!isset($fileArrays['name']) || !is_array($fileArrays['name'])) {
            return $files;
        }

        $fileCount = count($fileArrays['name']);
        for ($i = 0; $i < $fileCount; $i++) {
            $files[] = [
                'name' => $fileArrays['name'][$i],
                'type' => $fileArrays['type'][$i],
                'tmp_name' => $fileArrays['tmp_name'][$i],
                'error' => $fileArrays['error'][$i],
                'size' => $fileArrays['size'][$i]
            ];
        }
        return $files;
    }

    /**
     * Post /multiple endpoint logic
     */
    public static function uploadMultipleFiles($fileArrays, $groupeId = null)
    {
        $files = self::formatFileArray($fileArrays);
        $uploaded = [];
        foreach ($files as $file) {
            if ($file['error'] === UPLOAD_ERR_OK) {
                $uploaded[] = self::uploadFile($file, false, $groupeId);
            }
        }
        if (empty($uploaded) && !empty($files)) {
            throw new Exception("No files were uploaded successfully.");
        }
        return $uploaded;
    }

    /**
     * Post /multiple/temporary endpoint logic
     */
    public static function uploadTemporaryMultipleFiles($fileArrays)
    {
        $files = self::formatFileArray($fileArrays);
        $uploaded = [];
        foreach ($files as $file) {
            if ($file['error'] === UPLOAD_ERR_OK) {
                $uploaded[] = self::uploadFile($file, true);
            }
        }
        return $uploaded;
    }

    /**
     * Get /download/slug/:slug endpoint logic
     */
    public static function downloadFileBySlug($slug)
    {
        $upload = self::getFileByIdOrSlug($slug);
        self::streamFile($upload, 'attachment');
    }

    /**
     * Get /download/id/:id endpoint logic
     */
    public static function downloadFileById($id)
    {
        $upload = self::getFileByIdOrSlug($id);
        self::streamFile($upload, 'attachment');
    }

    /**
     * Get /view/slug/:slug endpoint logic
     */
    public static function viewFileBySlug($slug)
    {
        $upload = self::getFileByIdOrSlug($slug);
        self::streamFile($upload, 'inline');
    }

    /**
     * Get /view/id/:id endpoint logic
     */
    public static function viewFileById($id)
    {
        $upload = self::getFileByIdOrSlug($id);
        self::streamFile($upload, 'inline');
    }

    /**
     * Stream logic to output the file back to the client
     */
    private static function streamFile($upload, $disposition)
    {
        if (!$upload) {
            http_response_code(404);
            echo "File not found.";
            exit;
        }

        $absolutePath = __DIR__ . '/../' . $upload->getRelativePath();
        if (!file_exists($absolutePath)) {
            http_response_code(404);
            echo "File not found on disk.";
            exit;
        }

        header('Content-Type: ' . $upload->getMimetype());
        header('Content-Length: ' . $upload->getSize());
        $filename = explode('/', $upload->getRelativePath());
        $filename = end($filename);
        header('Content-Disposition: ' . $disposition . '; filename="' . $filename . '"');

        readfile($absolutePath);
        exit;
    }

    /**
     * Delete :id endpoint logic
     */
    public static function delete($id)
    {
        $upload = self::getFileByIdOrSlug($id);
        if ($upload) {
            return self::performDelete($upload);
        }
        return false;
    }

    /**
     * Delete slug/:slug endpoint logic
     */
    public static function deleteBySlug($slug)
    {
        $upload = self::getFileByIdOrSlug($slug);
        if ($upload) {
            return self::performDelete($upload);
        }
        return false;
    }

    /**
     * Hard delete file utility
     */
    private static function performDelete($upload)
    {
        global $pdo;
        $absolutePath = __DIR__ . '/../' . $upload->getRelativePath();
        if (file_exists($absolutePath)) {
            unlink($absolutePath);
        }

        $stmt = $pdo->prepare("DELETE FROM upload WHERE id = :id");
        $stmt->execute(['id' => $upload->getId()]);

        return $upload;
    }

    /**
     * Move file to a group
     */
    public static function moveToGroup($fileId, $groupeId = null)
    {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE upload SET groupeId = :groupeId WHERE id = :id");
        $stmt->execute([
            'groupeId' => $groupeId,
            'id' => $fileId
        ]);
        return self::getFileByIdOrSlug($fileId);
    }
}
?>