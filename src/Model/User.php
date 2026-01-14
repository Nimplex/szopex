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

    public function update_notifications(
        int $id,
        bool $message,
        bool $reports,
        bool $login,
        bool $listings,
        bool $administrative,
        bool $contact,
        bool $marketing,
        bool $mobile_app_notifications,
        bool $email_notifications
    ): bool {
        $stmt = $this->db->prepare(<<<SQL
        UPDATE users SET
            notifications_message = :message,
            notifications_reports = :reports,
            notifications_login = :login,
            notifications_listings = :listings,
            notifications_administrative = :administrative,
            notifications_contact = :contact,
            notifications_marketing = :marketing,
            mobile_app_notifications = :mobile_app_notifications,
            email_notifications = :email_notifications
        WHERE id = :id
        SQL);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':message', $message, PDO::PARAM_BOOL);
        $stmt->bindValue(':reports', $reports, PDO::PARAM_BOOL);
        $stmt->bindValue(':login', $login, PDO::PARAM_BOOL);
        $stmt->bindValue(':listings', $listings, PDO::PARAM_BOOL);
        $stmt->bindValue(':administrative', $administrative, PDO::PARAM_BOOL);
        $stmt->bindValue(':contact', $contact, PDO::PARAM_BOOL);
        $stmt->bindValue(':marketing', $marketing, PDO::PARAM_BOOL);
        $stmt->bindValue(':mobile_app_notifications', $mobile_app_notifications, PDO::PARAM_BOOL);
        $stmt->bindValue(':email_notifications', $email_notifications, PDO::PARAM_BOOL);
        $stmt->execute();

        return true;
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
