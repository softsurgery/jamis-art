<?php
require_once __DIR__ . '/../connect.php';
require_once __DIR__ . '/../models/User.php';

class UserController
{
    // ✅ Get all users
    public function getAll()
    {
        global $pdo;
        $sql = "SELECT * FROM user";
        try {
            $query = $pdo->query($sql);
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die("Erreur : " . $e->getMessage());
        }
    }

    // ✅ Add new user
    public function save($user)
    {
        global $pdo;
        $sql = "INSERT INTO user (firstName, lastName, email, passwordHash, active, role, artTypeId)
                VALUES (:firstName, :lastName, :email, :passwordHash, :active, :role, :artTypeId)";
        try {
            $query = $pdo->prepare($sql);
            $query->execute([
                ':firstName' => $user->getFirstName(),
                ':lastName' => $user->getLastName(),
                ':email' => $user->getEmail(),
                ':passwordHash' => $user->getPasswordHash(),
                ":active" => $user->getActive(),
                ':role' => $user->getRole(),
                ':artTypeId' => $user->getArtTypeId()
            ]);
        } catch (Exception $e) {
            die("Erreur lors de l'enregistrement : " . $e->getMessage());
        }
    }

    // ✅ Delete user by ID
    public function delete($id)
    {
        global $pdo;
        $sql = "DELETE FROM user WHERE id = :id";
        try {
            $query = $pdo->prepare($sql);
            $query->execute([':id' => $id]);
        } catch (Exception $e) {
            die("Erreur lors de la suppression : " . $e->getMessage());
        }
    }

    // ✅ Update user status
    public function updateStatus($id, $status)
    {
        global $pdo;
        $sql = "UPDATE user SET active = :status WHERE id = :id";
        try {
            $query = $pdo->prepare($sql);
            $query->execute([':id' => $id, ':status' => $status]);
        } catch (Exception $e) {
            die("Erreur lors de la mise à jour du statut : " . $e->getMessage());
        }
    }

    // ✅ Update user info
    public function update($id, $user)
    {
        global $pdo;
        $sql = "UPDATE user 
                SET firstName = :firstName, lastName = :lastName, email = :email, active = :active, role = :role, artTypeId = :artTypeId
                WHERE id = :id";
        try {
            $query = $pdo->prepare($sql);
            $query->execute([
                ':id' => $id,
                ':firstName' => $user->getFirstName(),
                ':lastName' => $user->getLastName(),
                ':email' => $user->getEmail(),
                ':active' => $user->getActive(),
                ':role' => $user->getRole(),
                ':artTypeId' => $user->getArtTypeId()
            ]);
        } catch (Exception $e) {
            die("Erreur lors de la mise à jour : " . $e->getMessage());
        }
    }

    // ✅ Get user by ID
    public function getById($id)
    {
        global $pdo;
        $sql = "SELECT * FROM user WHERE id = :id";
        try {
            $query = $pdo->prepare($sql);
            $query->execute([':id' => $id]);
            $data = $query->fetch(PDO::FETCH_ASSOC);
            if ($data) {
                return new User(
                    $data['id'],
                    $data['firstName'],
                    $data['lastName'],
                    $data['email'],
                    $data['passwordHash'],
                    $data['active'],
                    $data['role'],
                    $data['artTypeId']
                );
            }
            return null;
        } catch (Exception $e) {
            die("Erreur lors de la récupération : " . $e->getMessage());
        }
    }

    // ✅ Get user by Email
    public function getByEmail($email)
    {
        global $pdo;
        $sql = "SELECT * FROM user WHERE email = :email";
        try {
            $query = $pdo->prepare($sql);
            $query->execute([':email' => $email]);
            $data = $query->fetch(PDO::FETCH_ASSOC);
            if ($data) {
                return new User(
                    $data['id'],
                    $data['firstName'],
                    $data['lastName'],
                    $data['email'],
                    $data['passwordHash'],
                    $data['active'],
                    $data['role'],
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