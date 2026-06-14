<?php

class Article
{
    private $id;
    private $title;
    private $description;
    private $content;
    private $publishedAt;
    private $authorId;
    private $variant;
    private $cover;
    private $artTypeId;

    public function __construct($id, $title, $description, $content, $publishedAt, $authorId, $variant, $cover, $artTypeId)
    {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->content = $content;
        $this->publishedAt = $publishedAt;
        $this->authorId = $authorId;
        $this->variant = $variant;
        $this->description = $description;
        $this->cover = $cover;
        $this->artTypeId = $artTypeId;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getPublishedAt()
    {
        return $this->publishedAt;
    }

    public function getAuthorId()
    {
        return $this->authorId;
    }

    public function getVariant()
    {
        return $this->variant;
    }

    public function getCover()
    {
        return $this->cover;
    }

    public function getArtTypeId()
    {
        return $this->artTypeId;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function setPublishedAt($publishedAt)
    {
        $this->publishedAt = $publishedAt;
    }

    public function setAuthorId($authorId)
    {
        $this->authorId = $authorId;
    }

    public function setVariant($variant)
    {
        $this->variant = $variant;
    }

    public function setCover($cover)
    {
        $this->cover = $cover;
    }

    public function setArtTypeId($artTypeId)
    {
        $this->artTypeId = $artTypeId;
    }

}

