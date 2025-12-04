<?php

namespace App\Model;

use PDO;

class Activation extends BaseDBModel
{
    public function find_by_token(string $token): ?array
    {
        $stmt = $this->db->prepare(<<<SQL
        SELECT
            id,
            user_id,
            code_hash,
            NOW() > activation.expires_at as expired
        FROM activation WHERE code_hash = :code_hash
        SQL);
        $stmt->bindValue(':code_hash', $token, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function find_by_email(string $email): ?array
    {
        $stmt = $this->db->prepare(<<<SQL
        SELECT
            activation.id as id,
            activation.user_id as user_id,
            activation.code_hash as code_hash
            NOW() > activation.expires_at as expired
        FROM users
        INNER JOIN activation
            ON users.id = activation.user_id
        WHERE users.email = :email
        SQL);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function find_by_user_id(int $user_id): ?array
    {
        $stmt = $this->db->prepare(<<<SQL
        SELECT
            id,
            user_id,
            code_hash,
            NOW() > activation.expires_at as expired
        FROM activation WHERE user_id = :user_id
        SQL);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function find_by_id(int $id): ?array
    {
        $stmt = $this->db->prepare(<<<SQL
        SELECT
            id,
            user_id,
            code_hash,
            NOW() > activation.expires_at as expired
        FROM activation WHERE id = :id
        SQL);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function create(int $user_id): string
    {
        $stmt = $this->db->prepare(<<<SQL
        INSERT INTO activation (user_id, code_hash, expires_at)
        VALUES (:user_id, :token, NOW() + INTERVAL '15 minutes')
        SQL);
        $token = bin2hex(random_bytes(32));
        $hash = hash('sha256', $token);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':token', $hash, PDO::PARAM_STR);
        $stmt->execute();
        return $hash;
    }

    public function delete(int $id): bool
    {
        $res = $this->find_by_id($id);

        if (!$res) {
            return false;
        }

        try {
            $this->db->beginTransaction();
            $stmt = $this->db->prepare(<<<SQL
            DELETE FROM activation WHERE id = :id
            SQL);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $this->db->commit();
        } catch (\Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e; // propagate to caller
        }

        return true;
    }

    public function regenerate_expired(int $id): string | bool
    {
        $res = $this->delete($id);
        if (!$res) {
            return false;
        }
        return $this->create($res['user_id']);
    }

    public function activate(int $id): bool
    {
        $res = $this->find_by_id($id);

        if (!$res) {
            return false;
        }

        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare(<<<SQL
            UPDATE users SET active = true WHERE id = :user_id
            SQL);
            $stmt->bindValue(':user_id', $res['user_id'], PDO::PARAM_INT);

            $stmt = $this->db->prepare(<<<SQL
            DELETE FROM activation WHERE id = :id
            SQL);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $this->db->commit();
            return true;
        } catch (\Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e; // propagate to caller
        }
    }
}
