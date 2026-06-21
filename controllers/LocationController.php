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

    public function getAllByArtType($artTypeId)
    {
        global $pdo;
        $sql = "SELECT * FROM `location` WHERE artTypeId = :artTypeId";
        try {
            $query = $pdo->prepare($sql);
            $query->execute([':artTypeId' => $artTypeId]);
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die("Erreur : " . $e->getMessage());
        }
    }

    // ? Add new location
    public function save($location)
    {
        global $pdo;
        $sql = "INSERT INTO `location` (latitude, longitude, label, description, artTypeId)
                VALUES (:latitude, :longitude, :label, :description, :artTypeId)";
        try {
            $query = $pdo->prepare($sql);
            $query->execute([
                ':latitude' => $location->getLatitude(),
                ':longitude' => $location->getLongitude(),
                ':label' => $location->getLabel(),
                ':description' => $location->getDescription(),
                ':artTypeId' => $location->getArtTypeId()
            ]);
            $newLocationId = $pdo->lastInsertId();

            // Send Email Notifications
            try {
                require_once __DIR__ . '/UserController.php';
                require_once __DIR__ . '/ArtTypeController.php';
                require_once __DIR__ . '/EmailController.php';
                
                $userController = new UserController();
                $artTypeController = new ArtTypeController();
                $emailController = new EmailController();

                $artType = $artTypeController->getById($location->getArtTypeId());
                $artTypeLabel = $artType ? $artType->getLabel() : 'this category';

                $usersToNotify = $userController->getByArtTypeId($location->getArtTypeId());

                require_once __DIR__ . '/../lib/Env.php';
                $appUrl = Env::get('APP_URL', 'http://127.0.0.1/jamis-art');
                $mapUrl = $appUrl . '/views/landing/map.php';

                foreach ($usersToNotify as $user) {
                    $emailController->sendTemplateEmail(
                        $user->getEmail(),
                        "New Location Added: " . $location->getLabel(),
                        'new-location.php',
                        [
                            'name' => $user->getFirstName(),
                            'artTypeLabel' => $artTypeLabel,
                            'locationLabel' => $location->getLabel(),
                            'locationDescription' => $location->getDescription(),
                            'mapUrl' => $mapUrl
                        ]
                    );
                }
            } catch (Exception $emailEx) {
                error_log("Failed to send location notifications: " . $emailEx->getMessage());
            }

            return $newLocationId;
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
                label = :label,
                description = :description,
                artTypeId = :artTypeId
                WHERE id = :id";
        try {
            $query = $pdo->prepare($sql);
            $query->execute([
                ':id' => $id,
                ':latitude' => $location->getLatitude(),
                ':longitude' => $location->getLongitude(),
                ':label' => $location->getLabel(),
                ':description' => $location->getDescription(),
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
                    $data['label'],
                    $data['description'],
                    $data['artTypeId']
                );
            }
            return null;
        } catch (Exception $e) {
            die("Erreur lors de la rcupration : " . $e->getMessage());
        }
    }
}
?>