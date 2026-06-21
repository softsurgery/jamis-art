<?php
require_once __DIR__ . '/../connect.php';
require_once __DIR__ . '/../models/Article.php';
require_once __DIR__ . '/UserController.php';
require_once __DIR__ . '/ArtTypeController.php';
require_once __DIR__ . '/EmailController.php';

class ArticleController
{
    // ✅ Get all articles
    public function getAll()
    {
        global $pdo;
        $sql = "SELECT article.*, upload.relativePath AS coverPath
                FROM article
                LEFT JOIN upload ON article.cover = upload.id
                ORDER BY article.publishedAt DESC";
        try {
            $query = $pdo->query($sql);
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die("Erreur : " . $e->getMessage());
        }
    }

    // ✅ Get article by ID
    public function getById($id)
    {
        global $pdo;
        $sql = "SELECT article.*, upload.relativePath AS coverPath
                FROM article
                LEFT JOIN upload ON article.cover = upload.id
                WHERE article.id = :id";
        try {
            $query = $pdo->prepare($sql);
            $query->execute([':id' => $id]);
            return $query->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die("Erreur : " . $e->getMessage());
        }
    }

    // ✅ Get articles by author ID
    public function getByAuthorId($authorId)
    {
        global $pdo;
        $sql = "SELECT article.*, upload.relativePath AS coverPath
                FROM article
                LEFT JOIN upload ON article.cover = upload.id
                WHERE article.authorId = :authorId
                ORDER BY article.publishedAt DESC";
        try {
            $query = $pdo->prepare($sql);
            $query->execute([':authorId' => $authorId]);
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die("Erreur : " . $e->getMessage());
        }
    }

    // ✅ Get articles by art type ID
    public function getByArtTypeId($artTypeId)
    {
        global $pdo;
        $sql = "SELECT article.*, upload.relativePath AS coverPath
                FROM article
                LEFT JOIN upload ON article.cover = upload.id
                WHERE article.artTypeId = :artTypeId
                ORDER BY article.publishedAt DESC";
        try {
            $query = $pdo->prepare($sql);
            $query->execute([':artTypeId' => $artTypeId]);
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die("Erreur : " . $e->getMessage());
        }
    }

    // ✅ Add new article
    public function save($article)
    {
        global $pdo;
        $sql = "INSERT INTO article (title, description, content, publishedAt, authorId, variant, cover, artTypeId)
                VALUES (:title, :description, :content, :publishedAt, :authorId, :variant, :cover, :artTypeId)";
        try {
            $query = $pdo->prepare($sql);
            $query->execute([
                ':title' => $article->getTitle(),
                ':description' => $article->getDescription(),
                ':content' => $article->getContent(),
                ':publishedAt' => $article->getPublishedAt(),
                ':authorId' => $article->getAuthorId(),
                ':variant' => $article->getVariant(),
                ':cover' => $article->getCover(),
                ':artTypeId' => $article->getArtTypeId()
            ]);
            $newArticleId = $pdo->lastInsertId();

            // Send Email Notifications
            try {
                $userController = new UserController();
                $artTypeController = new ArtTypeController();
                $emailController = new EmailController();

                $artType = $artTypeController->getById($article->getArtTypeId());
                $artTypeLabel = $artType ? $artType->getLabel() : 'this category';

                $usersToNotify = $userController->getByArtTypeId($article->getArtTypeId());

                // Use the configured APP_URL or default to localhost if not set in Env
                require_once __DIR__ . '/../lib/Env.php';
                $appUrl = Env::get('APP_URL', 'http://127.0.0.1/jamis-art');
                $articleUrl = $appUrl . '/views/landing/read-article.php?id=' . $newArticleId;

                foreach ($usersToNotify as $user) {
                    $emailController->sendTemplateEmail(
                        $user->getEmail(),
                        "New Article: " . $article->getTitle(),
                        'new-article.php',
                        [
                            'name' => $user->getFirstName(),
                            'artTypeLabel' => $artTypeLabel,
                            'articleTitle' => $article->getTitle(),
                            'articleDescription' => $article->getDescription(),
                            'articleUrl' => $articleUrl
                        ]
                    );
                }
            } catch (Exception $emailEx) {
                // We catch the exception so that email failure doesn't prevent article creation
                error_log("Failed to send article notifications: " . $emailEx->getMessage());
            }

            return $newArticleId;
        } catch (Exception $e) {
            die("Erreur lors de l'enregistrement : " . $e->getMessage());
        }
    }

    // ✅ Update article
    public function update($article)
    {
        global $pdo;
        $sql = "UPDATE article 
                SET title = :title, description = :description, content = :content, 
                    publishedAt = :publishedAt, variant = :variant, cover = :cover, artTypeId = :artTypeId
                WHERE id = :id";
        try {
            $query = $pdo->prepare($sql);
            $query->execute([
                ':id' => $article->getId(),
                ':title' => $article->getTitle(),
                ':description' => $article->getDescription(),
                ':content' => $article->getContent(),
                ':publishedAt' => $article->getPublishedAt(),
                ':variant' => $article->getVariant(),
                ':cover' => $article->getCover(),
                ':artTypeId' => $article->getArtTypeId()
            ]);
        } catch (Exception $e) {
            die("Erreur lors de la mise à jour : " . $e->getMessage());
        }
    }

    // ✅ Delete article by ID
    public function delete($id)
    {
        global $pdo;
        $sql = "DELETE FROM article WHERE id = :id";
        try {
            $query = $pdo->prepare($sql);
            $query->execute([':id' => $id]);
        } catch (Exception $e) {
            die("Erreur lors de la suppression : " . $e->getMessage());
        }
    }

    // ✅ Get filtered articles with pagination
    public function getFilteredArticles($artTypeId, $search, $filter, $offset, $limit)
    {
        global $pdo;

        $conditions = [];
        $params = [];

        if (!empty($artTypeId)) {
            $conditions[] = 'article.artTypeId = :artTypeId';
            $params[':artTypeId'] = $artTypeId;
        }

        if (!empty($search)) {
            $conditions[] = '(article.title LIKE :search OR article.description LIKE :search OR article.content LIKE :search)';
            $params[':search'] = '%' . $search . '%';
        }

        if (!empty($filter) && $filter !== 'All') {
            $conditions[] = '(article.variant = :filter)';
            $params[':filter'] = $filter;
        }

        $whereClause = count($conditions) > 0 ? 'WHERE ' . implode(' AND ', $conditions) : '';

        $sql = "SELECT article.*, upload.relativePath AS coverPath
                FROM article
                LEFT JOIN upload ON article.cover = upload.id
                $whereClause
                ORDER BY article.publishedAt DESC
                LIMIT :limit OFFSET :offset";

        try {
            $query = $pdo->prepare($sql);

            // Bind parameters
            foreach ($params as $key => $value) {
                $query->bindValue($key, $value);
            }

            $query->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
            $query->bindValue(':offset', (int) $offset, PDO::PARAM_INT);

            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die("Erreur : " . $e->getMessage());
        }
    }
}
