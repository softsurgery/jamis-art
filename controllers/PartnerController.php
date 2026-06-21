<?php
require_once __DIR__ . '/../connect.php';
require_once __DIR__ . '/../models/Partner.php';

class PartnerController
{
    public function getAll()
    {
        global $pdo;
        $sql = "SELECT partners.*, upload.relativePath as logoPath 
                FROM partners 
                LEFT JOIN upload ON partners.logoId = upload.id";
        try {
            $query = $pdo->query($sql);
            $results = $query->fetchAll(PDO::FETCH_ASSOC);
            $partners = [];
            foreach ($results as $row) {
                $partners[] = new Partner(
                    $row['id'],
                    $row['label'],
                    $row['logoId'],
                    $row['logoPath']
                );
            }
            return $partners;
        } catch (Exception $e) {
            die("Erreur : " . $e->getMessage());
        }
    }

    public function save($partner)
    {
        global $pdo;
        $sql = "INSERT INTO partners (label, logoId)
                VALUES (:label, :logoId)";
        try {
            $query = $pdo->prepare($sql);
            $query->execute([
                ':label' => $partner->getLabel(),
                ':logoId' => $partner->getLogoId()
            ]);
        } catch (Exception $e) {
            die("Erreur lors de l'enregistrement : " . $e->getMessage());
        }
    }

    public function delete($id)
    {
        global $pdo;
        $sql = "DELETE FROM partners WHERE id = :id";
        try {
            $query = $pdo->prepare($sql);
            $query->execute([':id' => $id]);
        } catch (Exception $e) {
            die("Erreur lors de l'enregistrement : " . $e->getMessage());
        }
    }

    public function update($id, $partner)
    {
        global $pdo;
        $sql = "UPDATE partners 
                SET label = :label,
                logoId = :logoId
                WHERE id = :id";
        try {
            $query = $pdo->prepare($sql);
            $query->execute([
                ':id' => $id,
                ':label' => $partner->getLabel(),
                ':logoId' => $partner->getLogoId()
            ]);
        } catch (Exception $e) {
            die("Erreur lors de la mise à jour : " . $e->getMessage());
        }
    }

    public function getById($id)
    {
        global $pdo;
        $sql = "SELECT partners.*, upload.relativePath as logoPath 
                FROM partners 
                LEFT JOIN upload ON partners.logoId = upload.id 
                WHERE partners.id = :id";
        try {
            $query = $pdo->prepare($sql);
            $query->execute([':id' => $id]);
            $data = $query->fetch(PDO::FETCH_ASSOC);
            if ($data) {
                return new Partner(
                    $data['id'],
                    $data['label'],
                    $data['logoId'],
                    $data['logoPath']
                );
            }
            return null;
        } catch (Exception $e) {
            die("Erreur lors de la récupération : " . $e->getMessage());
        }
    }
}
?>
