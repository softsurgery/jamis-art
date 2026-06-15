<?php
require_once __DIR__ . '/../connect.php';
require_once __DIR__ . '/../models/Resource.php';

class ResourceController
{
    public function getAll()
    {
        global $pdo;
        $sql = "SELECT * FROM `resources`";
        try {
            $query = $pdo->query($sql);
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    public function getAllByArtType($artTypeId)
    {
        global $pdo;
        $sql = "SELECT * FROM `resources` WHERE artTypeId = :artTypeId";
        try {
            $query = $pdo->prepare($sql);
            $query->execute([':artTypeId' => $artTypeId]);
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    public function save($resource)
    {
        global $pdo;
        $sql = "INSERT INTO `resources` (uploadId, artTypeId, label, description)
                VALUES (:uploadId, :artTypeId, :label, :description)";
        try {
            $query = $pdo->prepare($sql);
            $query->execute([
                ':uploadId' => $resource->getUploadId(),
                ':artTypeId' => $resource->getArtTypeId(),
                ':label' => $resource->getLabel(),
                ':description' => $resource->getDescription()
            ]);
        } catch (Exception $e) {
            die("Error saving resource: " . $e->getMessage());
        }
    }

    public function delete($id)
    {
        global $pdo;
        $sql = "DELETE FROM `resources` WHERE id = :id";
        try {
            $query = $pdo->prepare($sql);
            $query->execute([':id' => $id]);
        } catch (Exception $e) {
            die("Error deleting resource: " . $e->getMessage());
        }
    }

    public function update($id, $resource)
    {
        global $pdo;
        $sql = "UPDATE `resources` 
                SET uploadId = :uploadId,
                artTypeId = :artTypeId,
                label = :label,
                description = :description
                WHERE id = :id";
        try {
            $query = $pdo->prepare($sql);
            $query->execute([
                ':id' => $id,
                ':uploadId' => $resource->getUploadId(),
                ':artTypeId' => $resource->getArtTypeId(),
                ':label' => $resource->getLabel(),
                ':description' => $resource->getDescription()
            ]);
        } catch (Exception $e) {
            die("Error updating resource: " . $e->getMessage());
        }
    }

    public function getById($id)
    {
        global $pdo;
        $sql = "SELECT * FROM `resources` WHERE id = :id";
        try {
            $query = $pdo->prepare($sql);
            $query->execute([':id' => $id]);
            $data = $query->fetch(PDO::FETCH_ASSOC);
            if ($data) {
                return new Resource(
                    $data['id'],
                    $data['uploadId'],
                    $data['artTypeId'],
                    $data['label'],
                    $data['description']
                );
            }
            return null;
        } catch (Exception $e) {
            die("Error getting resource: " . $e->getMessage());
        }
    }
}
