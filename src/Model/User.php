<?php

namespace App\Model;

use PDO;

class User
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function create(string $login, string $email, string $password): bool
    {
        $stmt = $this->db->prepare('INSERT INTO users (login, email, password_hash) VALUES (?, ?, ?)');
        // https://www.php.net/manual/en/function.password-hash.php
        // I decided that ARGON2ID is stronger than bcrypt
        $hash = password_hash($password, PASSWORD_ARGON2ID, [
            'memory_cost' => 1 << 16,
            'time_cost' => 4,
            'threads' => 2
        ]);
        return $stmt->execute([$login, $email, $hash]);
    }
}
