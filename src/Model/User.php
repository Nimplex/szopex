<?php

namespace App\Model;

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

    public function exists(int $id): bool
    {
        $stmt = $this->db->prepare('SELECT 1 FROM users WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->rowCount() >= 1;
    }


    // I think it will be much safer not to expose password hashes etc. in responses even if it's not shown to user
    public function get_profile(int $id): ?array
    {
        $stmt = $this->db->prepare(<<<SQL
        SELECT
            u.id,
            u.display_name,
            u.created_at,
            (
                SELECT COUNT(*) FROM listings l WHERE l.user_id = u.id
            ) as listing_count,
            COALESCE(p.file_id, 'default') as picture_id
        FROM users u
        LEFT JOIN profile_pictures p ON p.user_id = u.id
        WHERE u.id = ?
        SQL);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
}
