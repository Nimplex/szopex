<?php

namespace App\Service;

use App\Model\User;

class AuthService
{
    private User $userModel;

    public function __construct(User $userModel)
    {
        $this->userModel = $userModel;
    }

    public function register(string $login, string $email, string $password): bool
    {
        return $this->userModel->create($login, $email, $password);
    }

    public function login(string $email, string $password): bool
    {
        $user = $this->userModel->findByEmail($email);
        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            return true;
        }
        return false;
    }
}
