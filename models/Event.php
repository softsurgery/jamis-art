<?php

class Event
{
    private $id;
    private $title;
    private $uploadId;
    private $description;
    private $markdown;
    private $artTypeId;
    private $createdAt;

    public function __construct($id = null, $title = '', $uploadId = null, $description = '', $markdown = '', $artTypeId = null, $createdAt = null)
    {
        $this->id = $id;
        $this->title = $title;
        $this->uploadId = $uploadId;
        $this->description = $description;
        $this->markdown = $markdown;
        $this->artTypeId = $artTypeId;
        $this->createdAt = $createdAt;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getUploadId()
    {
        return $this->uploadId;
    }

    public function setUploadId($uploadId)
    {
        $this->uploadId = $uploadId;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getMarkdown()
    {
        return $this->markdown;
    }

    public function setMarkdown($markdown)
    {
        $this->markdown = $markdown;
    }

    public function getArtTypeId()
    {
        return $this->artTypeId;
    }

    public function setArtTypeId($artTypeId)
    {
        $this->artTypeId = $artTypeId;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }
}
