<?php
require_once __DIR__ . '/../connect.php';

class StatController
{
    public function getDashboardStats()
    {
        global $pdo;
        $stats = [
            'users' => 0,
            'articles' => 0,
            'locations' => 0,
            'partners' => 0,
            'messages' => 0,
            'article_views' => 0,
            'messages_today' => 0,
            'messages_month' => 0,
            'messages_year' => 0,
            'storage_bytes' => 0
        ];

        try {
            // Get total users
            $query = $pdo->query("SELECT COUNT(*) FROM `user`");
            $stats['users'] = $query ? $query->fetchColumn() : 0;

            // Get total articles
            $query = $pdo->query("SELECT COUNT(*) FROM `article`");
            $stats['articles'] = $query ? $query->fetchColumn() : 0;

            // Get total locations
            $query = $pdo->query("SELECT COUNT(*) FROM `location`");
            $stats['locations'] = $query ? $query->fetchColumn() : 0;

            // Get total partners
            $query = $pdo->query("SELECT COUNT(*) FROM `partners`");
            $stats['partners'] = $query ? $query->fetchColumn() : 0;

            // Get total support messages
            $query = $pdo->query("SELECT COUNT(*) FROM `support-messages`");
            $stats['messages'] = $query ? $query->fetchColumn() : 0;

            // Get total article views
            $query = $pdo->query("SELECT SUM(`count`) FROM `counter` WHERE `type` = 'article'");
            $stats['article_views'] = $query ? ($query->fetchColumn() ?: 0) : 0;

            // Messages Today
            $query = $pdo->query("SELECT COUNT(*) FROM `support-messages` WHERE DATE(`createdAt`) = CURDATE()");
            $stats['messages_today'] = $query ? $query->fetchColumn() : ($pdo->errorInfo()[2] ?? 0);

            // Messages Month
            $query = $pdo->query("SELECT COUNT(*) FROM `support-messages` WHERE MONTH(`createdAt`) = MONTH(CURDATE()) AND YEAR(`createdAt`) = YEAR(CURDATE())");
            $stats['messages_month'] = $query ? $query->fetchColumn() : ($pdo->errorInfo()[2] ?? 0);

            // Messages Year
            $query = $pdo->query("SELECT COUNT(*) FROM `support-messages` WHERE YEAR(`createdAt`) = YEAR(CURDATE())");
            $stats['messages_year'] = $query ? $query->fetchColumn() : ($pdo->errorInfo()[2] ?? 0);

            // Total Storage (bytes)
            $query = $pdo->query("SELECT SUM(`size`) FROM `upload`");
            $stats['storage_bytes'] = $query ? ($query->fetchColumn() ?: 0) : ($pdo->errorInfo()[2] ?? 0);

        } catch (Exception $e) {
            $stats['error'] = $e->getMessage();
        }

        return $stats;
    }

    public function formatStorage($bytes)
    {
        if (!is_numeric($bytes)) {
            return $bytes; // Return the error string if a query failed
        }
        
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            return $bytes . ' B';
        } elseif ($bytes == 1) {
            return $bytes . ' B';
        } else {
            return '0 B';
        }
    }
}
?>