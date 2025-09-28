<?php

namespace App\Model;

use PDO;

class Listing extends BaseDBModel
{
    public const int PER_PAGE = 10;
    public const int MIN_PAGE = 1;
    public const int MAX_PAGE = 1000;

    private function _listAll(int $limit, int $offset): array
    {
        $stmt = $this->db->prepare('SELECT * FROM listings ORDER BY created_at DESC LIMIT :limit OFFSET :offset');
        // I'm using bindValue to enforce use of INT
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

    public function listAll(int $page): ?array
    {
        $page = max(Listing::MIN_PAGE, min(Listing::MAX_PAGE, $page));
        $offset = ($page - 1) * Listing::PER_PAGE;
        return $this->_listAll(Listing::PER_PAGE, $offset);
    }
}
