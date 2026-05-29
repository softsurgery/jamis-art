<?php
require_once __DIR__ . '/../connect.php';
require_once __DIR__ . '/../models/Location.php';

class LocationController
{
    // ? Get all locations
    public function getAll()
    {
        global $pdo;
        $sql = "SELECT * FROM `location`";
        try {
            $query = $pdo->query($sql);
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die("Erreur : " . $e->getMessage());
        }
    }

    // ? Add new location
    public function save($location)
    {
        global $pdo;
        $sql = "INSERT INTO `location` (latitude, longitude, artTypeId)
                VALUES (:latitude, :longitude, :artTypeId)";
        try {
            $query = $pdo->prepare($sql);
            $query->execute([
                ':latitude' => $location->getLatitude(),
                ':longitude' => $location->getLongitude(),
                ':artTypeId' => $location->getArtTypeId()
            ]);
        } catch (Exception $e) {
            die("Erreur lors de l'enregistrement : " . $e->getMessage());
        }
    }

    // ? Delete location by ID
    public function delete($id)
    {
        global $pdo;
        $sql = "DELETE FROM `location` WHERE id = :id";
        try {
            $query = $pdo->prepare($sql);
            $query->execute([':id' => $id]);
        } catch (Exception $e) {
            die("Erreur lors de la suppression : " . $e->getMessage());
        }
    }

    // ? Update location info
    public function update($id, $location)
    {
        global $pdo;
        $sql = "UPDATE `location` 
                SET latitude = :latitude,
                longitude = :longitude,
                artTypeId = :artTypeId
                WHERE id = :id";
        try {
            $query = $pdo->prepare($sql);
            $query->execute([
                ':id' => $id,
                ':latitude' => $location->getLatitude(),
                ':longitude' => $location->getLongitude(),
                ':artTypeId' => $location->getArtTypeId()
            ]);
        } catch (Exception $e) {
            die("Erreur lors de la mise à jour : " . $e->getMessage());
        }
    }

    // ? Get location by ID
    public function getById($id)
    {
        global $pdo;
        $sql = "SELECT * FROM `location` WHERE id = :id";
        try {
            $query = $pdo->prepare($sql);
            $query->execute([':id' => $id]);
            $data = $query->fetch(PDO::FETCH_ASSOC);
            if ($data) {
                return new Location(
                    $data['id'],
                    $data['latitude'],
                    $data['longitude'],
                    $data['artTypeId']
                );
            }
            return null;
        } catch (Exception $e) {
            die("Erreur lors de la récupération : " . $e->getMessage());
        }
    }
}
?>
