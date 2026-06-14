<?php
class Upload
{
    private $id;
    private $slug;
    private $relativePath;
    private $mimeType;
    private $size;
    private $isTemporary;
    private $isPrivate;
    private $groupeId;

    public function __construct($id, $slug, $relativePath, $mimeType, $size, $isTemporary = false, $isPrivate = false, $groupeId = null)
    {
        $this->id = $id;
        $this->slug = $slug;
        $this->relativePath = $relativePath;
        $this->mimeType = $mimeType;
        $this->size = $size;
        $this->isTemporary = $isTemporary;
        $this->isPrivate = $isPrivate;
        $this->groupeId = $groupeId;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function getRelativePath()
    {
        return $this->relativePath;
    }

    public function getMimetype()
    {
        return $this->mimeType;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function isTemporary()
    {
        return $this->isTemporary;
    }

    public function isPrivate()
    {
        return $this->isPrivate;
    }

    public function getGroupeId()
    {
        return $this->groupeId;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    public function setRelativePath($relativePath)
    {
        $this->relativePath = $relativePath;
    }

    public function setMimetype($mimeType)
    {
        $this->mimeType = $mimeType;
    }

    public function setSize($size)
    {
        $this->size = $size;
    }

    public function setIsTemporary($isTemporary)
    {
        $this->isTemporary = $isTemporary;
    }

    public function setIsPrivate($isPrivate)
    {
        $this->isPrivate = $isPrivate;
    }

    public function setGroupeId($groupeId)
    {
        $this->groupeId = $groupeId;
    }
}
?>