<?php
require_once __DIR__ . '/../connect.php';
require_once __DIR__ . '/../models/UploadGroupe.php';

class UploadGroupController
{
    /**
     * Get all groups
     */
    public static function findAll()
    {
        global $pdo;
        $stmt = $pdo->query("SELECT * FROM `upload-group` ORDER BY name ASC");
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $groups = [];
        foreach ($results as $row) {
            $groups[] = new UploadGroup($row['id'], $row['name'], $row['parent']);
        }
        return $groups;
    }

    /**
     * Get group by ID
     */
    public static function getById($id)
    {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM `upload-group` WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return new UploadGroup($row['id'], $row['name'], $row['parent']);
        }
        return null;
    }

    /**
     * Get all groups that have a specific parent
     */
    public static function getByParent($parentId)
    {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM `upload-group` WHERE parent = :parent ORDER BY name ASC");
        $stmt->execute(['parent' => $parentId]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $groups = [];
        foreach ($results as $row) {
            $groups[] = new UploadGroup($row['id'], $row['name'], $row['parent']);
        }
        return $groups;
    }

    /**
     * Get root groups (parent = 0)
     */
    public static function getRootGroups()
    {
        return self::getByParent(0);
    }

    /**
     * Create a new group
     */
    public static function create($name, $parentId = 0)
    {
        global $pdo;

        if (empty(trim($name))) {
            throw new Exception("Group name cannot be empty.");
        }

        $stmt = $pdo->prepare("INSERT INTO `upload-group` (name, parent) VALUES (:name, :parent)");
        $stmt->execute([
            'name' => $name,
            'parent' => $parentId
        ]);

        $id = $pdo->lastInsertId();
        return new UploadGroup($id, $name, $parentId);
    }

    /**
     * Update a group
     */
    public static function update($id, $name, $parentId = null)
    {
        global $pdo;

        if (empty(trim($name))) {
            throw new Exception("Group name cannot be empty.");
        }

        $group = self::getById($id);
        if (!$group) {
            throw new Exception("Group not found.");
        }

        if ($parentId === null) {
            $parentId = $group->getParent();
        }

        $stmt = $pdo->prepare("UPDATE `upload-group` SET name = :name, parent = :parent WHERE id = :id");
        $stmt->execute([
            'name' => $name,
            'parent' => $parentId,
            'id' => $id
        ]);

        return self::getById($id);
    }

    /**
     * Delete a group (and optionally move its children)
     */
    public static function delete($id, $moveChildrenToParent = true)
    {
        global $pdo;

        $group = self::getById($id);
        if (!$group) {
            throw new Exception("Group not found.");
        }

        if ($moveChildrenToParent) {
            // Move all files from this group to its parent
            $parentId = $group->getParent();
            $stmt = $pdo->prepare("UPDATE upload SET groupeId = :parent WHERE groupeId = :id");
            $stmt->execute([
                'parent' => $parentId,
                'id' => $id
            ]);

            // Move all child groups to the parent
            $stmt = $pdo->prepare("UPDATE `upload-group` SET parent = :parent WHERE parent = :id");
            $stmt->execute([
                'parent' => $parentId,
                'id' => $id
            ]);
        } else {
            // Delete all files in this group
            $stmt = $pdo->prepare("DELETE FROM upload WHERE groupeId = :id");
            $stmt->execute(['id' => $id]);

            // Delete all child groups recursively
            $childGroups = self::getByParent($id);
            foreach ($childGroups as $child) {
                self::delete($child->getId(), false);
            }
        }

        // Delete the group
        $stmt = $pdo->prepare("DELETE FROM `upload-group` WHERE id = :id");
        $stmt->execute(['id' => $id]);

        return $group;
    }

    /**
     * Get breadcrumb path for a group
     */
    public static function getBreadcrumbPath($groupId)
    {
        $path = [];
        $current = self::getById($groupId);

        while ($current) {
            array_unshift($path, $current);
            if ($current->getParent() && $current->getParent() !== 0) {
                $current = self::getById($current->getParent());
            } else {
                break;
            }
        }

        return $path;
    }
}
?>