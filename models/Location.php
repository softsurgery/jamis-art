<?php

class Location
{
    private $id;
    private $latitude;
    private $longitude;
    private $label;
    private $description;
    private $artTypeId;

    public function __construct($id, $latitude, $longitude, $label, $description, $artTypeId)
    {
        $this->id = $id;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->label = $label;
        $this->description = $description;
        $this->artTypeId = $artTypeId;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getLatitude()
    {
        return $this->latitude;
    }

    public function getLongitude()
    {
        return $this->longitude;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getArtTypeId()
    {
        return $this->artTypeId;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    }

    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
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

