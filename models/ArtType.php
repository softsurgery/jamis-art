<?php

class ArtType
{

    private $id;
    private $label;
    private $colorValue;

    public function __construct($id, $label, $colorValue = null)
    {
        $this->id = $id;
        $this->label = $label;
        $this->colorValue = $colorValue;
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
}