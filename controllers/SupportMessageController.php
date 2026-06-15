<?php
require_once __DIR__ . '/../connect.php';
require_once __DIR__ . '/../models/SupportMessage.php';

class SupportMessageController
{
    public function save($supportMessage)
    {
        global $pdo;
        $sql = "INSERT INTO `support-messages` (subject, message, userId, email, artTypeId)
                VALUES (:subject, :message, :userId, :email, :artTypeId)";
        try {
            $query = $pdo->prepare($sql);
            $query->execute([
                ':subject' => $supportMessage->getSubject(),
                ':message' => $supportMessage->getMessage(),
                ':userId' => $supportMessage->getUserId(),
                ':email' => $supportMessage->getEmail(),
                ':artTypeId' => $supportMessage->getArtTypeId()
            ]);
            return true;
        } catch (Exception $e) {
            // Log error or handle it
            return false;
        }
    }

    public function getAll()
    {
        global $pdo;
        $sql = "SELECT * FROM `support-messages`";
        try {
            $query = $pdo->query($sql);
            $results = $query->fetchAll(PDO::FETCH_ASSOC);
            $messages = [];
            foreach ($results as $data) {
                $messages[] = new SupportMessage(
                    $data['id'],
                    $data['subject'],
                    $data['message'],
                    $data['userId'],
                    $data['email'],
                    $data['artTypeId']
                );
            }
            return $messages;
        } catch (Exception $e) {
            return [];
        }
    }

    public function delete($id)
    {
        global $pdo;
        $sql = "DELETE FROM `support-messages` WHERE id = :id";
        try {
            $query = $pdo->prepare($sql);
            $query->execute([':id' => $id]);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
