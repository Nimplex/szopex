<?php

namespace App\Model;

use PDO;

class Chat extends BaseDBModel
{
    public function find_by_id(int $id): ?array
    {
        $stmt = $this->db->prepare(<<<SQL
        SELECT * FROM chats WHERE id = ?
        SQL);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function find_by_user(int $user_id): ?array
    {
        $stmt = $this->db->prepare(<<<SQL
        SELECT
            c.*,
            b.display_name as buyer_name,
            s.display_name as seller_name,
            (c.seller_id = :user_id) AS is_seller
        FROM chats c
        JOIN users b
            ON c.buyer_id = b.id
        JOIN users s
            ON c.seller_id = s.id
        WHERE c.seller_id = :user_id
            OR c.buyer_id = :user_id
        SQL);
        $stmt->execute([
            ':user_id' => $user_id
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: null;
    }

    public function find_standalone(int $seller_id, int $buyer_id): ?array
    {
        $stmt = $this->db->prepare(<<<SQL
        SELECT * FROM chats WHERE seller_id = ? AND buyer_id = ? AND listing = null
        SQL);
        $stmt->execute([ $seller_id, $buyer_id ]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function find_listings(int $seller_id, int $buyer_id, int $listing_id): ?array
    {
        $stmt = $this->db->prepare(<<<SQL
        SELECT * FROM chats WHERE seller_id = ? AND buyer_id = ? AND listing = ?
        SQL);
        $stmt->execute([ $seller_id, $buyer_id, $listing_id ]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function get_messages(int $chat_id): ?array
    {
        $stmt = $this->db->prepare(<<<SQL
        SELECT
            m.id,
            m.content,
            m.created_at,
            u.display_name,
            (c.seller_id = u.id) AS is_seller
        FROM chats c
        LEFT JOIN messages m
            ON m.chat_id = c.id
        LEFT JOIN users u
            ON m.sender_id = u.id
        WHERE c.id = ?
        SQL);
        $stmt->execute([$chat_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function create(int $seller_id, int $buyer_id): int | bool
    {
        $stmt = $this->db->prepare(<<<SQL
        INSERT INTO chats(seller_id, buyer_id) VALUES(?, ?)
        SQL);
        
        $res = $stmt->execute([$seller_id, $buyer_id]);

        if ($res) {
            return $this->db->lastInsertId();
        }

        return false;
    }
}
