<?php

class Resource
{
    private $id;
    private $uploadId;
    private $artTypeId;
    private $label;
    private $description;

    public function __construct($id, $uploadId, $artTypeId, $label, $description)
    {
        $this->id = $id;
        $this->uploadId = $uploadId;
        $this->artTypeId = $artTypeId;
        $this->label = $label;
        $this->description = $description;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUploadId()
    {
        return $this->uploadId;
    }

    public function getArtTypeId()
    {
        return $this->artTypeId;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setUploadId($uploadId)
    {
        $this->uploadId = $uploadId;
    }

    public function setArtTypeId($artTypeId)
    {
        $this->artTypeId = $artTypeId;
    }

    public function setLabel($label)
    {
        $this->label = $label;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }
}
