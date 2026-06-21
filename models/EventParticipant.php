<?php

class EventParticipant
{
    private $id;
    private $eventId;
    private $userId;
    private $email;
    private $createdAt;

    public function __construct($id, $eventId, $userId, $email, $createdAt = null)
    {
        $this->id = $id;
        $this->eventId = $eventId;
        $this->userId = $userId;
        $this->email = $email;
        $this->createdAt = $createdAt;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getEventId()
    {
        return $this->eventId;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setEventId($eventId)
    {
        $this->eventId = $eventId;
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }
}
