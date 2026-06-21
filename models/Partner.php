<?php

class Partner
{

    private $id;
    private $label;
    private $logoId;
    private $logoPath;

    public function __construct($id, $label, $logoId = null, $logoPath = null)
    {
        $this->id = $id;
        $this->label = $label;
        $this->logoId = $logoId;
        $this->logoPath = $logoPath;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function getLogoId()
    {
        return $this->logoId;
    }

    public function getLogoPath()
    {
        return $this->logoPath;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setLabel($label)
    {
        $this->label = $label;
    }

    public function setLogoId($logoId)
    {
        $this->logoId = $logoId;
    }
    
    public function setLogoPath($logoPath)
    {
        $this->logoPath = $logoPath;
    }
}
