<?php

namespace App\Model;

use PDO;

class Reports extends BaseDBModel
{
    public const int PER_PAGE = 10;
    public function find_by_id(int $id): ?array
    {
        $stmt = $this->db->prepare(<<<SQL
        SELECT
            id,
            r.reporter_id,
            r.reported_id,
            r.listing_id,
            r.reason,
            r.created_at,
            rr.display_name AS reporter_name,
            rd.display_name AS reported_name,
            l.id AS listing_id,
            l.title AS listing_title, 
            (r.listing_id IS NOT NULL) AS contains_listing,
        LEFT JOIN users rr ON r.reporter_id = rr.id
        LEFT JOIN users rd ON r.reported_id = rd.id
        LEFT JOIN listings l ON r.listing_id = l.id
        FROM user_reports r
        WHERE r.id = :id
        SQL);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function find_by_user_id(int $user_id): ?array
    {
        $stmt = $this->db->prepare(<<<SQL
        SELECT
            id,
            r.reporter_id,
            r.reported_id,
            r.listing_id,
            r.reason,
            r.created_at,
            rr.display_name AS reporter_name,
            rd.display_name AS reported_name,
            l.id AS listing_id,
            l.title AS listing_title, 
            (r.listing_id IS NOT NULL) AS contains_listing,
        LEFT JOIN users rr ON r.reporter_id = rr.id
        LEFT JOIN users rd ON r.reported_id = rd.id
        LEFT JOIN listings l ON r.listing_id = l.id
        FROM user_reports r
        WHERE r.reported_id = :user_id OR r.reporter_id = :user_id
        SQL);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: null;
    }

    public function get_all(int $page, int $limit = Reports::PER_PAGE): array
    {
        $offset = ($page - 1) * $limit;
        $stmt = $this->db->prepare(<<<SQL
        SELECT
            id,
            r.reporter_id,
            r.reported_id,
            r.listing_id,
            r.reason,
            r.created_at,
            rr.display_name AS reporter_name,
            rd.display_name AS reported_name,
            l.id AS listing_id,
            l.title AS listing_title, 
            (r.listing_id IS NOT NULL) AS contains_listing,
        LEFT JOIN users rr ON r.reporter_id = rr.id
        LEFT JOIN users rd ON r.reported_id = rd.id
        LEFT JOIN listings l ON r.listing_id = l.id
        FROM user_reports r
        ORDER BY r.created_at
        LIMIT :limit OFFSET :offset
        SQL);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: null;
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare(<<<SQL
        DELETE FROM user_reports WHERE id = :id
        SQL);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    /**
     * @throws \PDOException
     * @return bool success - false if exists
     */
    public function create(int $reporter_id, int $reported_id, ?int $listing_id, string $reason): void
    {
        $stmt = $this->db->prepare(<<<SQL
        INSERT INTO user_reports (reporter_id, reported_id, listing_id, reason) VALUES (:reporter_id, :reported_id, :listing_id, :reason)
        SQL);
        $stmt->bindValue(':reporter_id', $reporter_id, PDO::PARAM_INT);
        $stmt->bindValue(':reported_id', $reported_id, PDO::PARAM_INT);
        if ($listing_id == null) {
            $stmt->bindValue(':listing_id', $listing_id, PDO::PARAM_NULL);
        } else {
            $stmt->bindValue(':listing_id', $listing_id, PDO::PARAM_INT);
        }
        $stmt->bindValue(':reason', $reason, PDO::PARAM_INT);
        $stmt->execute();
    }
}
