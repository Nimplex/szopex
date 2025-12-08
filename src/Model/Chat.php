<?php

namespace App\Model;

use PDO;

class Chat extends BaseDBModel
{
    public const int PER_PAGE = 10;
    public const int MIN_PAGE = 1;
    public const int MAX_PAGE = 1000;

    public function find_by_id(int $id): ?array
    {
        $stmt = $this->db->prepare(<<<SQL
        SELECT
            c.id AS chat_id,
            s.display_name AS seller_name,
            s.id AS seller_id,
            b.display_name AS buyer_name,
            b.id AS buyer_id,
            l.title AS listing_title,
            l.id AS listing_id,
            l.price AS listing_price,
            cv.file_id AS cover_file_id,
            (c.listing_id IS NOT NULL) AS contains_listing,
            COALESCE(sp.file_id, 'default') AS seller_pfp_file_id,
            COALESCE(bp.file_id, 'default') AS buyer_pfp_file_id
        FROM chats c
        LEFT JOIN users s ON c.seller_id = s.id
        LEFT JOIN users b ON c.buyer_id = b.id
        LEFT JOIN listings l ON c.listing_id = l.id
        LEFT JOIN covers cv ON cv.listing_id = l.id AND cv.main = TRUE
        LEFT JOIN profile_pictures sp ON sp.user_id = s.id
        LEFT JOIN profile_pictures bp ON bp.user_id = b.id
        WHERE c.id = :id
        SQL);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function find_by_user(int $user_id): ?array
    {
        $stmt = $this->db->prepare(<<<SQL
        SELECT
            c.id AS chat_id,
            s.display_name AS seller_name,
            s.id AS seller_id,
            b.display_name AS buyer_name,
            b.id AS buyer_id,
            l.title AS listing_title,
            l.id AS listing_id,
            l.price AS listing_price,
            cv.file_id AS cover_file_id,
            (c.seller_id = :user_id) AS is_seller,
            (c.listing_id IS NOT NULL) AS contains_listing,
            COALESCE(sp.file_id, 'default') AS seller_pfp_file_id,
            COALESCE(bp.file_id, 'default') AS buyer_pfp_file_id
        FROM chats c
        LEFT JOIN users s ON c.seller_id = s.id
        LEFT JOIN users b ON c.buyer_id = b.id
        LEFT JOIN listings l ON c.listing_id = l.id
        LEFT JOIN covers cv ON cv.listing_id = l.id AND cv.main = TRUE
        LEFT JOIN profile_pictures sp ON sp.user_id = s.id
        LEFT JOIN profile_pictures bp ON bp.user_id = b.id
        WHERE s.id = :user_id OR b.id = :user_id
        SQL);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: null;
    }

    public function find_standalone(int $seller_id, int $buyer_id): ?array
    {
        $stmt = $this->db->prepare(<<<SQL
        SELECT * FROM chats WHERE seller_id = :seller_id AND buyer_id = :buyer_id AND listing_id = null
        SQL);
        $stmt->bindValue(':seller_id', $seller_id, PDO::PARAM_INT);
        $stmt->bindValue(':buyer_id', $buyer_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function find_listings(int $seller_id, int $buyer_id, int $listing_id): ?array
    {
        $stmt = $this->db->prepare(<<<SQL
        SELECT * FROM chats WHERE seller_id = :seller_id AND buyer_id = :buyer_id AND listing_id = :listing_id
        SQL);
        $stmt->bindValue(':seller_id', $seller_id, PDO::PARAM_INT);
        $stmt->bindValue(':buyer_id', $buyer_id, PDO::PARAM_INT);
        $stmt->bindValue(':listing_id', $listing_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
 
    public function get_messages(int $chat_id, int $page, int $limit = Chat::PER_PAGE): ?array
    {
        $offset = ($page - 1) * $limit;
        $stmt = $this->db->prepare(<<<SQL
        SELECT
            m.id,
            m.content,
            m.created_at,
            u.display_name,
            u.id as user_id,
            (c.seller_id = u.id) AS is_seller
        FROM chats c
        JOIN messages m ON m.chat_id = c.id
        JOIN users u ON m.sender_id = u.id
        WHERE c.id = :chat_id
        ORDER BY m.created_at DESC
        LIMIT :limit OFFSET :offset
        SQL);
        $stmt->bindValue(':chat_id', $chat_id, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: null;
    }

    public function create(int $seller_id, int $buyer_id, int|null $listing_id = null): int | bool
    {
        $stmt = $this->db->prepare(<<<SQL
        INSERT INTO chats(seller_id, buyer_id, listing_id) VALUES(:seller_id, :buyer_id, :listing_id)
        SQL);
        $stmt->bindValue(':seller_id', $seller_id, PDO::PARAM_INT);
        $stmt->bindValue(':buyer_id', $buyer_id, PDO::PARAM_INT);
        $stmt->bindValue(':listing_id', $listing_id, PDO::PARAM_INT);
        $res = $stmt->execute();

        if ($res) {
            return $this->db->lastInsertId();
        }

        return false;
    }

    public function add_message(int $chat_id, int $sender_id, string $content): int | bool
    {
        $stmt = $this->db->prepare(<<<SQL
        INSERT INTO messages(chat_id, sender_id, content) VALUES(:chat_id, :sender_id, :content)
        SQL);

        $stmt->bindValue(':chat_id', $chat_id, PDO::PARAM_INT);
        $stmt->bindValue(':sender_id', $sender_id, PDO::PARAM_INT);
        $stmt->bindValue(':content', $content, PDO::PARAM_STR);
        $res = $stmt->execute();

        if ($res) {
            return $this->db->lastInsertId();
        }

        return false;
    }
}
