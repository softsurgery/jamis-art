<?php
require_once __DIR__ . '/../connect.php';
require_once __DIR__ . '/../models/Article.php';

class ArticleController
{
    // ✅ Get all articles
    public function getAll()
    {
        global $pdo;
        $sql = "SELECT * FROM article ORDER BY publishedAt DESC";
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
        $sql = "SELECT * FROM article WHERE id = :id";
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
        $sql = "SELECT * FROM article WHERE authorId = :authorId ORDER BY publishedAt DESC";
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
        $sql = "SELECT * FROM article WHERE artTypeId = :artTypeId ORDER BY publishedAt DESC";
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
        $sql = "INSERT INTO article (title, description, content, publishedAt, authorId, variant, artTypeId)
                VALUES (:title, :description, :content, :publishedAt, :authorId, :variant, :artTypeId)";
        try {
            $query = $pdo->prepare($sql);
            $query->execute([
                ':title' => $article->getTitle(),
                ':description' => $article->getDescription(),
                ':content' => $article->getContent(),
                ':publishedAt' => $article->getPublishedAt(),
                ':authorId' => $article->getAuthorId(),
                ':variant' => $article->getVariant(),
                ':artTypeId' => $article->getArtTypeId()
            ]);
            return $pdo->lastInsertId();
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
                    publishedAt = :publishedAt, variant = :variant, artTypeId = :artTypeId
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
}
