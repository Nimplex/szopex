<?php

namespace App\Model;

use PDO;

class Auth extends BaseDBModel
{
    private function _findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    private function _create(string $login, string $email, string $password): bool
    {
        $stmt = $this->db->prepare('INSERT INTO users (login, email, password_hash) VALUES (:login, :email, :password_hash)');

        // https://www.php.net/manual/en/function.password-hash.php
        // I decided that ARGON2ID is stronger than bcrypt
        $hash = password_hash($password, PASSWORD_ARGON2ID, [
            'memory_cost' => 1 << 16,
            'time_cost' => 4,
            'threads' => 2
        ]);

        return $stmt->execute([
            ':login' => $login,
            ':email' => $email,
            ':password_hash' => $hash
        ]);
    }

    // --- public methods ---
    
    public function register(string $login, string $email, string $password): bool
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Invalid email format");
        }
        
        if (strlen($password) < 8) {
            throw new \InvalidArgumentException("Password has to be at least 8 characters long");
        }
        
        return $this->_create($login, $email, $password);
    }

    public function login(string $email, string $password): int
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Invalid email format");
        }

        $user = $this->_findByEmail($email);

        if ($user && password_verify($password, $user['password_hash'])) {
            return $user['id'];
        }

        return -1;
    }

    /**
     * @param array<int,string|null> $request
     */
    public function register_from_request(array $request): string
    {
        $login = $request['login'] ?? null;
        $email = $request['email'] ?? null;
        $password = $request['password'] ?? null;

        if (!$login || !$email || !$password) {
            return "Missing fields";
        }

        try {
            $this->register($login, $email, $password);
            return "Registration successful";
        } catch (\InvalidArgumentException $e) {
            return "Error: {$e->getMessage()}";
        }
    }

    /**
     * @param array<int,string|null> $request
     */
    public function login_from_request(array $request): string
    {
        $email = $request['email'] ?? null;
        $password = $request['password'] ?? null;

        try {
            $res = $this->login($email, $password);

            if ($res == -1) {
                return "User doesn't exist or the password is invalid";
            }

            $_SESSION['user_id'] = $res;

            return "Logged in successfully";
        } catch (\InvalidArgumentException $e) {
            return "Error: {$e->getMessage()}";
        }
    }
}
