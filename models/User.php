<?php
class User
{
    private $id;
    private $firstName;
    private $lastName;
    private $email;
    private $passwordHash;
    private $active;
    private $artTypeId;

    public function __construct($id, $firstName, $lastName, $email, $passwordHash, $active, $artTypeId)
    {
        $this->id = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->passwordHash = $passwordHash;
        $this->active = $active;
        $this->artTypeId = $artTypeId;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getPasswordHash()
    {
        return $this->passwordHash;
    }

    public function getActive()
    {
        return $this->active;
    }

    public function getArtTypeId()
    {
        return $this->artTypeId;
    }


    public function setId($id)
    {
        $this->id = $id;
    }

    public function setFirstname($firstName)
    {
        $this->firstName = $firstName;
    }

    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setPasswordHash($passwordHash)
    {
        $this->passwordHash = $passwordHash;
    }

    public function setArtTypeId($artTypeId)
    {
        $this->artTypeId = $artTypeId;
    }

    public function setActive($active)
    {
        $this->active = $active;
    }
}
?>