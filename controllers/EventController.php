<?php
require_once __DIR__ . '/../connect.php';
require_once __DIR__ . '/../models/Event.php';

class EventController
{
    // ✅ Get all events
    public function getAll()
    {
        global $pdo;
        $sql = "SELECT event.*, upload.relativePath AS coverPath
                FROM event
                LEFT JOIN upload ON event.uploadId = upload.id
                ORDER BY event.createdAt DESC";
        try {
            $query = $pdo->query($sql);
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die("Erreur : " . $e->getMessage());
        }
    }

    // ✅ Get event by ID
    public function getById($id)
    {
        global $pdo;
        $sql = "SELECT event.*, upload.relativePath AS coverPath
                FROM event
                LEFT JOIN upload ON event.uploadId = upload.id
                WHERE event.id = :id";
        try {
            $query = $pdo->prepare($sql);
            $query->execute([':id' => $id]);
            return $query->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die("Erreur : " . $e->getMessage());
        }
    }

    // ✅ Get events by art type ID
    public function getByArtTypeId($artTypeId)
    {
        global $pdo;
        $sql = "SELECT event.*, upload.relativePath AS coverPath
                FROM event
                LEFT JOIN upload ON event.uploadId = upload.id
                WHERE event.artTypeId = :artTypeId
                ORDER BY event.createdAt DESC";
        try {
            $query = $pdo->prepare($sql);
            $query->execute([':artTypeId' => $artTypeId]);
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die("Erreur : " . $e->getMessage());
        }
    }

    // ✅ Add new event
    public function save($event)
    {
        global $pdo;
        $sql = "INSERT INTO event (title, description, markdown, uploadId, artTypeId, createdAt)
                VALUES (:title, :description, :markdown, :uploadId, :artTypeId, :createdAt)";
        try {
            $query = $pdo->prepare($sql);
            $query->execute([
                ':title' => $event->getTitle(),
                ':description' => $event->getDescription(),
                ':markdown' => $event->getMarkdown(),
                ':uploadId' => $event->getUploadId(),
                ':artTypeId' => $event->getArtTypeId(),
                ':createdAt' => $event->getCreatedAt() ?: date('Y-m-d H:i:s')
            ]);
            return $pdo->lastInsertId();
        } catch (Exception $e) {
            die("Erreur lors de l'enregistrement : " . $e->getMessage());
        }
    }

    // ✅ Update event
    public function update($event)
    {
        global $pdo;
        $sql = "UPDATE event 
                SET title = :title, description = :description, markdown = :markdown, 
                    uploadId = :uploadId, artTypeId = :artTypeId
                WHERE id = :id";
        try {
            $query = $pdo->prepare($sql);
            $query->execute([
                ':id' => $event->getId(),
                ':title' => $event->getTitle(),
                ':description' => $event->getDescription(),
                ':markdown' => $event->getMarkdown(),
                ':uploadId' => $event->getUploadId(),
                ':artTypeId' => $event->getArtTypeId()
            ]);
        } catch (Exception $e) {
            die("Erreur lors de la mise à jour : " . $e->getMessage());
        }
    }

    // ✅ Delete event by ID
    public function delete($id)
    {
        global $pdo;
        $sql = "DELETE FROM event WHERE id = :id";
        try {
            $query = $pdo->prepare($sql);
            $query->execute([':id' => $id]);
        } catch (Exception $e) {
            die("Erreur lors de la suppression : " . $e->getMessage());
        }
    }
}
