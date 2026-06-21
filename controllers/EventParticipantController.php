<?php
require_once __DIR__ . '/../connect.php';
require_once __DIR__ . '/../models/EventParticipant.php';
require_once __DIR__ . '/EmailController.php';

class EventParticipantController
{
    // ✅ Add new participant
    public function save($participant)
    {
        global $pdo;
        
        // Check if already registered
        if ($participant->getUserId()) {
            $checkSql = "SELECT id FROM `event-participants` WHERE eventId = :eventId AND userId = :userId";
            $checkQuery = $pdo->prepare($checkSql);
            $checkQuery->execute([
                ':eventId' => $participant->getEventId(),
                ':userId' => $participant->getUserId()
            ]);
        } else {
            $checkSql = "SELECT id FROM `event-participants` WHERE eventId = :eventId AND email = :email";
            $checkQuery = $pdo->prepare($checkSql);
            $checkQuery->execute([
                ':eventId' => $participant->getEventId(),
                ':email' => $participant->getEmail()
            ]);
        }

        if ($checkQuery->rowCount() > 0) {
            return ["success" => false, "message" => "You are already registered for this event."];
        }

        $sql = "INSERT INTO `event-participants` (eventId, userId, email, createdAt)
                VALUES (:eventId, :userId, :email, :createdAt)";
        try {
            $query = $pdo->prepare($sql);
            $query->execute([
                ':eventId' => $participant->getEventId(),
                ':userId' => $participant->getUserId(),
                ':email' => $participant->getEmail(),
                ':createdAt' => $participant->getCreatedAt() ?: date('Y-m-d H:i:s')
            ]);
            
            $insertedId = $pdo->lastInsertId();
            return ["success" => true, "id" => $insertedId];
        } catch (Exception $e) {
            return ["success" => false, "message" => "Error during registration: " . $e->getMessage()];
        }
    }

    // ✅ Get participants by event ID
    public function getByEventId($eventId)
    {
        global $pdo;
        $sql = "SELECT p.*, u.firstName, u.lastName, u.email as userEmail
                FROM `event-participants` p
                LEFT JOIN user u ON p.userId = u.id
                WHERE p.eventId = :eventId
                ORDER BY p.createdAt DESC";
        try {
            $query = $pdo->prepare($sql);
            $query->execute([':eventId' => $eventId]);
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
}
