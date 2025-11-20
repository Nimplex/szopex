<?php

namespace App\Model;

require $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';

use PDO;

class User extends BaseDBModel
{
    public function find_by_id(int $id): ?array
    {
        $stmt = $this->db->prepare(<<<SQL
        SELECT * FROM users WHERE id = ?
        SQL);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function find_by_email(string $email): ?array
    {
        $stmt = $this->db->prepare(<<<SQL
        SELECT * FROM users WHERE email = ?
        SQL);

        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
    
    public function find_by_login(string $login): ?array
    {
        $stmt = $this->db->prepare(<<<SQL
        SELECT * FROM users WHERE login = ?
        SQL);

        $stmt->execute([$login]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function email_repeats(string $email): bool
    {
        return !empty($this->find_by_email($email));
    }

    public function login_repeats(string $login): bool
    {
        return !empty($this->find_by_login($login));
    }

    public function create(string $login, string $display_name, string $email, string $password): int | bool
    {
        $stmt = $this->db->prepare(<<<SQL
        INSERT INTO users(login, display_name, email, password_hash)
        VALUES (:login, :display_name, :email, :password_hash)
        SQL);

        $hash = password_hash($password, PASSWORD_ARGON2ID, [
            'memory_cost' => 1 << 16,
            'time_cost' => 4,
            'threads' => 2,
        ]);

        $res = $stmt->execute([
            ':login' => $login,
            ':display_name' => $display_name,
            ':email' => $email,
            ':password_hash' => $hash
        ]);

        if ($res) {
            return $this->db->lastInsertId();
        }

        return false;
    }

    // I think it will be much safer not to expose password hashes etc. in responses even if it's not shown to user
    public function get_profile(int $id): ?array
    {
        $stmt = $this->db->prepare(<<<SQL
        SELECT
            display_name,
            created_at,
            (
                SELECT COUNT(*) FROM listings WHERE listings.user_id = users.id
            ) as listing_count
        FROM users WHERE id = ?
        SQL);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
}
