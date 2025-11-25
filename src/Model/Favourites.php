<?php

namespace App\Model;

use PDO;

class Favourites extends BaseDBModel
{
    public function find_by_id(int $id): ?array
    {
        $stmt = $this->db->prepare(<<<SQL
        SELECT * FROM favourites WHERE id = ?
        SQL);

        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function find_by_user_id(int $user_id): ?array
    {
        $stmt = $this->db->prepare(<<<SQL
        SELECT
            l.id as listing_id,
            l.user_id,
            l.title,
            l.price,
            l.created_at,
            l.updated_at,
            l.attributes,
            l.active,
            u.display_name,
            c.file_id AS cover_file_id
        FROM favourites f
        LEFT JOIN listings l
            ON f.listing_id = l.id
        LEFT JOIN users u
            ON f.user_id = u.id
        LEFT JOIN covers c
            ON c.listing_id = l.id AND c.main = TRUE
        WHERE f.user_id = ?
        ORDER BY f.created_at DESC
        SQL);

        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: null;
    }

    public function exists(int $listing_id, int $user_id): ?array
    {
        $stmt = $this->db->prepare(<<<SQL
        SELECT * FROM favourites WHERE user_id = ? AND listing_id = ?
        SQL);

        $stmt->execute([$user_id, $listing_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function delete(int $id): bool
    {
        try {
            $this->db->beginTransaction();
 
            $stmt = $this->db->prepare(<<<SQL
            DELETE FROM favourites WHERE id = ?
            SQL);
            $stmt->execute([$id]);

            $this->db->commit();
            return true;
        } catch (\Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e; // propagate to caller
        }
    }

    /**
     * @throws \PDOException
     * @return bool success - false if exists
     */
    public function create(int $listing_id, int $user_id): bool
    {
        $sql = "INSERT INTO favourites (user_id, listing_id) VALUES (:user_id, :listing_id)";
        $stmt = $this->db->prepare($sql);

        try {
            $stmt->execute([
                ':user_id' => $user_id,
                ':listing_id' => $listing_id
            ]);
            return true;
        } catch (\PDOException $e) {
            if ($e->getCode() === '23505') {
                return false; // already exists
            }
            throw $e;
        }
    }
}
