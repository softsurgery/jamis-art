<?php

class UploadGroup
{
    private $id;
    private $name;
    private $parent;

    public function __construct($id, $name, $parent = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->parent = $parent;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setParent($parent)
    {
        $this->parent = $parent;
    }
}

