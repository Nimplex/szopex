<?php

namespace App\Model;

use PDO;

class User extends BaseDBModel
{
    public function find_by_id(int $id): ?array
    {
        $stmt = $this->db->prepare(<<<SQL
        SELECT * FROM users WHERE id = :id
        SQL);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function find_by_email(string $email): ?array
    {
        $stmt = $this->db->prepare(<<<SQL
        SELECT * FROM users WHERE email = :email
        SQL);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
    
    public function find_by_login(string $login): ?array
    {
        $stmt = $this->db->prepare(<<<SQL
        SELECT * FROM users WHERE login = :login
        SQL);
        $stmt->bindValue(':login', $login, PDO::PARAM_STR);
        $stmt->execute();
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

        $stmt->bindValue(':login', $login, PDO::PARAM_STR);
        $stmt->bindValue(':display_name', $display_name, PDO::PARAM_STR);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->bindValue(':password_hash', $hash, PDO::PARAM_STR);

        $res = $stmt->execute();

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
            u.id,
            u.display_name,
            u.created_at,
            u.description,
            (
                SELECT COUNT(*) FROM listings l WHERE l.user_id = u.id
            ) as listing_count,
            COALESCE(p.file_id, 'default') as picture_id
        FROM users u
        LEFT JOIN profile_pictures p ON p.user_id = u.id
        WHERE u.id = :id
        SQL);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    // implementation to be used only in administrator panel
    public function admin_get_all(): array
    {
        $stmt = $this->db->prepare(<<<SQL
        SELECT
            u.id,
            u.login,
            u.display_name,
            u.email,
            u.role,
            u.created_at,
            (
                SELECT COUNT(*) FROM listings l WHERE l.user_id = u.id
            ) as listing_count
        FROM users u
        SQL);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: null;
    }
}
