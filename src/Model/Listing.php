<?php

namespace App\Model;

use PDO;

class Listing
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function listAll(int $limit, int $offset): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM listings ORDER BY created_at DESC LIMIT :limit OFFSET :offset');
        // I'm using bindValue to enforce use of INT
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: null;
    }

    public function findByID(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM listings WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function create(int $user_id, string $title, string $price, string $description): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO listings (user_id, title, price, description) VALUES (:user_id, :title, :price, :description)'
        );

        return $stmt->execute([
            ':user_id' => $user_id,
            ':title' => $title,
            ':price' => $price,
            ':description' => $description
        ]);
    }
}
