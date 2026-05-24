<?php

require_once __DIR__ . '/UserController.php';

class AuthController
{
    private $userController;

    public function __construct()
    {
        $this->userController = new UserController();
    }

    public function register($firstName, $lastName, $email, $password, $artTypeId)
    {
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $user = new User(null, $firstName, $lastName, $email, $passwordHash, $artTypeId);
        $this->userController->save($user);
    }

    public function login($email, $password)
    {
        $user = $this->userController->getByEmail($email);
        if ($user && password_verify($password, $user->getPasswordHash())) {
            return $user;
        }
        return null;
    }
}