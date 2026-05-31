<?php
require_once __DIR__ . '/../connect.php';
require_once __DIR__ . '/../models/ArtType.php';

class ArtTypeController
{
    // ✅ Get all art types
    public function getAll()
    {
        global $pdo;
        $sql = "SELECT `art-type`.*, upload.relativePath as uploadPath 
                FROM `art-type` 
                LEFT JOIN upload ON `art-type`.uploadId = upload.id";
        try {
            $query = $pdo->query($sql);
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die("Erreur : " . $e->getMessage());
        }
    }

    // ✅ Add new art type
    public function save($artType)
    {
        global $pdo;
        $sql = "INSERT INTO `art-type` (label,colorValue,uploadId)
                VALUES (:label, :colorValue, :uploadId)";
        try {
            $query = $pdo->prepare($sql);
            $query->execute([
                ':label' => $artType->getLabel(),
                ':colorValue' => $artType->getColorValue(),
                ':uploadId' => $artType->getUploadId()
            ]);
        } catch (Exception $e) {
            die("Erreur lors de l'enregistrement : " . $e->getMessage());
        }
    }

    // ✅ Delete art type by ID
    public function delete($id)
    {
        global $pdo;
        $sql = "DELETE FROM `art-type` WHERE id = :id";
        try {
            $query = $pdo->prepare($sql);
            $query->execute([':id' => $id]);
        } catch (Exception $e) {
            die("Erreur lors de l'enregistrement : " . $e->getMessage());
        }
    }

    // ✅ Update art type info
    public function update($id, $artType)
    {
        global $pdo;
        $sql = "UPDATE `art-type` 
                SET label = :label,
                colorValue = :colorValue,
                uploadId = :uploadId
                WHERE id = :id";
        try {
            $query = $pdo->prepare($sql);
            $query->execute([
                ':id' => $id,
                ':label' => $artType->getLabel(),
                ':colorValue' => $artType->getColorValue(),
                ':uploadId' => $artType->getUploadId()
            ]);
        } catch (Exception $e) {
            die("Erreur lors de la mise à jour : " . $e->getMessage());
        }
    }

    // ✅ Get art type by ID
    public function getById($id)
    {
        global $pdo;
        $sql = "SELECT * FROM `art-type` WHERE id = :id";
        try {
            $query = $pdo->prepare($sql);
            $query->execute([':id' => $id]);
            $data = $query->fetch(PDO::FETCH_ASSOC);
            if ($data) {
                return new ArtType(
                    $data['id'],
                    $data['label'],
                    $data['colorValue'],
                    $data['uploadId']
                );
            }
            return null;
        } catch (Exception $e) {
            die("Erreur lors de la récupération : " . $e->getMessage());
        }
    }
}
?>