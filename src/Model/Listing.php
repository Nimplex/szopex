<?php

namespace App\Model;

use PDO;

class Listing extends BaseDBModel
{
    public const int PER_PAGE = 10;
    public const int MIN_PAGE = 1;
    public const int MAX_PAGE = 1000;
    public const int MIN_TITLE_LEN = 8;
    public const int MAX_TITLE_LEN = 100;
    public const int MIN_DESC_LEN = 8;
    public const int MAX_DESC_LEN = 1000;

    private function _listAll(int $limit, int $offset): array
    {
        $stmt = $this->db->prepare(
            'SELECT 
            l.id AS listing_id,
            l.user_id,
            l.title,
            l.price,
            l.description,
            l.created_at,
            c.file_id AS cover_file_id
         FROM listings l
         LEFT JOIN covers c 
            ON c.listing_id = l.id 
           AND c.main = TRUE
         ORDER BY l.created_at DESC
         LIMIT :limit OFFSET :offset'
        );       // I'm using bindValue to enforce use of INT
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function _findByID(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM listings WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    private function _listByUser(int $user_id): array
    {
        $stmt = $this->db->prepare('SELECT * FROM listings WHERE user_id = ?');
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function _create(int $user_id, string $title, string $price, string $description): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO listings (user_id, title, price, description) VALUES (:user_id, :title, :price, :description)'
        );

        return $stmt->execute([
            ':user_id' => $user_id,
            ':title' => $title,
            ':price' => $price,
            ':description' => $description,
        ]);
    }

    // --- public methods ---

    public function listAll(int $page): array
    {
        $page = max(Listing::MIN_PAGE, min(Listing::MAX_PAGE, $page));
        $offset = ($page - 1) * Listing::PER_PAGE;
        return $this->_listAll(Listing::PER_PAGE, $offset);
    }

    public function listByUser(int $user_id): array
    {
        return $this->_listByUser($user_id);
    }

    public function create(string $title, string $price, string $description): void
    {
        if (!$title || !$price || !$description) {
            throw new \InvalidArgumentException("Not enough arguments");
        }

        if (strlen($title) < Listing::MIN_TITLE_LEN || strlen($title) > Listing::MAX_TITLE_LEN) {
            throw new \InvalidArgumentException(
                "Nieprawidłowa długość tytułu; musi być pomiędzy ${Listing::MIN_TITLE_LEN} a ${Listing::MAX_TITLE_LEN}",
                1
            );
        }

        if (strlen($description) < Listing::MIN_DESC_LEN || strlen($description) > Listing::MAX_DESC_LEN) {
            throw new \InvalidArgumentException(
                "Nieprawidłowa długość opisu; musi być pomiędzy ${Listing::MIN_TITLE_LEN} a ${Listing::MAX_TITLE_LEN}",
                1
            );
        }

        if (!preg_match('/^\d+(?:(?:,|\.)\d\d)?$/', $price)) {
            throw new \InvalidArgumentException("Nieprawidłowy format ceny", 1);
        }

        $parsed_price = str_replace('.', ',', $price);

        $res = $this->_create($_SESSION['user_id'], $title, $parsed_price, $description);
    }
}
