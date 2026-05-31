<?php

class ArtType
{

    private $id;
    private $label;
    private $colorValue;
    private $uploadId;

    public function __construct($id, $label, $colorValue = null, $uploadId = null)
    {
        $this->id = $id;
        $this->label = $label;
        $this->colorValue = $colorValue;
        $this->uploadId = $uploadId;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function getColorValue()
    {
        return $this->colorValue;
    }

    public function getUploadId()
    {
        return $this->uploadId;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setLabel($label)
    {
        $this->label = $label;
    }

    public function setColorValue($colorValue)
    {
        $this->colorValue = $colorValue;
    }

    public function setUploadId($uploadId)
    {
        $this->uploadId = $uploadId;
    }
}