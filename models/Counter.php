<?php
class Counter
{
    private $id;
    private $type;
    private $entryId;
    private $count;

    public function __construct($id, $type, $entryId, $count)
    {
        $this->id = $id;
        $this->type = $type;
        $this->entryId = $entryId;
        $this->count = $count;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getEntryId()
    {
        return $this->entryId;
    }

    public function getCount()
    {
        return $this->count;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function setEntryId($entryId)
    {
        $this->entryId = $entryId;
    }

    public function setCount($count)
    {
        $this->count = $count;
    }
}
?>
