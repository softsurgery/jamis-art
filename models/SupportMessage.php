<?php

class SupportMessage
{
    private $id;
    private $subject;
    private $message;
    private $userId;
    private $email;
    private $artTypeId;

    public function __construct($id, $subject, $message, $userId, $email, $artTypeId)
    {
        $this->id = $id;
        $this->subject = $subject;
        $this->message = $message;
        $this->userId = $userId;
        $this->email = $email;
        $this->artTypeId = $artTypeId;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getArtTypeId()
    {
        return $this->artTypeId;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setArtTypeId($artTypeId)
    {
        $this->artTypeId = $artTypeId;
    }
}
