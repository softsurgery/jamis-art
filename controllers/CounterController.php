<?php
require_once __DIR__ . '/../connect.php';
require_once __DIR__ . '/../models/Counter.php';

class CounterController
{
    public function increment($type, $entryId)
    {
        global $pdo;
        $sql = "INSERT INTO `counter` (`type`, `entryId`, `count`) 
                VALUES (:type, :entryId, 1) 
                ON DUPLICATE KEY UPDATE `count` = `count` + 1";
        try {
            $query = $pdo->prepare($sql);
            $query->execute([
                ':type' => $type,
                ':entryId' => $entryId
            ]);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function getCount($type, $entryId)
    {
        global $pdo;
        $sql = "SELECT `count` FROM `counter` WHERE `type` = :type AND `entryId` = :entryId";
        try {
            $query = $pdo->prepare($sql);
            $query->execute([
                ':type' => $type,
                ':entryId' => $entryId
            ]);
            $count = $query->fetchColumn();
            return $count ? (int)$count : 0;
        } catch (Exception $e) {
            return 0;
        }
    }

    public function getTotalCountByType($type)
    {
        global $pdo;
        $sql = "SELECT SUM(`count`) FROM `counter` WHERE `type` = :type";
        try {
            $query = $pdo->prepare($sql);
            $query->execute([':type' => $type]);
            $count = $query->fetchColumn();
            return $count ? (int)$count : 0;
        } catch (Exception $e) {
            return 0;
        }
    }
}
?>
