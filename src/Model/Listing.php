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

    private function _listAll(int $limit, int $offset, int $current_user_id): array
    {
        $stmt = $this->db->prepare(<<<SQL
        SELECT
            l.id AS listing_id,
            l.user_id,
            l.title,
            l.price,
            l.created_at,
            l.attributes,
            u.display_name,
            c.file_id AS cover_file_id,
            EXISTS (
                SELECT 1
                FROM favourites f
                WHERE f.listing_id = l.id AND f.user_id = :current_user_id
            ) AS is_favourited
        FROM listings l
        LEFT JOIN users u
            ON u.id = l.user_id
        LEFT JOIN covers c
            ON c.listing_id = l.id AND c.main = TRUE
        ORDER BY l.created_at DESC
        LIMIT :limit OFFSET :offset
        SQL);
        $stmt->bindValue(':current_user_id', $current_user_id, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
   
    private function _findByID(int $id, int $current_user_id): ?array
    {
        $stmt = $this->db->prepare(<<<SQL
        SELECT
            l.id AS listing_id,
            l.user_id,
            l.title,
            l.price,
            l.description,
            l.created_at,
            l.attributes,
            l.active,
            u.display_name,
            c.file_id AS cover_file_id,
            EXISTS (
                SELECT 1
                FROM favourites f
                WHERE f.listing_id = l.id AND f.user_id = :current_user_id
            ) AS is_favourited
        FROM listings l
        LEFT JOIN users u
            ON u.id = l.user_id
        LEFT JOIN covers c
            ON c.listing_id = l.id
        WHERE l.id = :id
        SQL);
        $stmt->execute([
            ':id' => $id,
            ':current_user_id' => $current_user_id,
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    private function _listCoversByID(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM covers WHERE listing_id = ?');
        $stmt->execute([$id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function _listByUser(int $user_id): array
    {
        $stmt = $this->db->prepare(<<<SQL
        SELECT
            l.id AS listing_id,
            l.user_id,
            l.title,
            l.price,
            l.description,
            l.created_at,
            l.attributes,
            l.active,
            c.file_id AS cover_file_id
        FROM listings l
        LEFT JOIN covers c
            ON c.listing_id = l.id AND c.main = TRUE
        WHERE l.user_id = ?
        SQL);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @return int<1,max>|false
     */
    private function _create(int $user_id, string $title, string $price, string $description): int|false
    {
        $stmt = $this->db->prepare(
            'INSERT INTO listings (user_id, title, price, description) VALUES (:user_id, :title, :price, :description)'
        );

        $res = $stmt->execute([
            ':user_id' => $user_id,
            ':title' => $title,
            ':price' => $price,
            ':description' => $description,
        ]);

        if ($res) {
            return $this->db->lastInsertId();
        }

        return false;
    }

    /**
     * @param array<int,mixed> $files
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    private function _addCovers(int $listing_id, array $files): bool
    {
        if (!isset($files) || !isset($files['images']) || empty($files['images']) || !isset($files['images']['error'])) {
            return false;
        }

        foreach ($files['images']['error'] as $i => $err) {
            if ($err === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            if ($err !== UPLOAD_ERR_OK) {
                throw new \RuntimeException("Upload error (code: $err)");
            }

            $tmp_name = $files['images']['tmp_name'][$i];
            $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($tmp_name);
            $allowed = ['image/jpeg' => '.jpg', 'image/png' => '.png'];

            if (!isset($allowed[$mime])) {
                throw new \InvalidArgumentException('Unsupported file type');
            }

            $size = $files['images']['size'][$i];
            if ($size <= 0 || $size > 5_000_000) {
                throw new \InvalidArgumentException('Invalid file size');
            }

            $new_name = sprintf('%d-%s%s', $listing_id, bin2hex(random_bytes(8)), $allowed[$mime]);
            $target = $_SERVER['DOCUMENT_ROOT'] . '/../storage/covers/' . $new_name;

            if (!move_uploaded_file($tmp_name, $target)) {
                throw new \RuntimeException('Failed to move uploaded file');
            }
            
            $stmt = $this->db->prepare(
                'INSERT INTO covers (listing_id, file_id, main) VALUES (:listing_id, :file_id, :main)'
            );

            $res = $stmt->execute([
                ':listing_id' => $listing_id,
                ':file_id' => $new_name,
                ':main' => ($i == 0) ? 'true' : 'false',
            ]);

            if (!$res) {
                throw new \RuntimeException("Couldn't insert record to database");
            }
        }

        return true;
    }

    // --- public methods ---

    public function exists(int $id): bool
    {
        $stmt = $this->db->prepare('SELECT 1 FROM listings WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->rowCount() >= 1;
    }

    public function count(): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM listings');
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function countPages(): int
    {
        $rows = $this->count();
        return ceil($rows / Listing::PER_PAGE);
    }

    public function listAll(int $page, int $user_id): array
    {
        $page = max(Listing::MIN_PAGE, min(Listing::MAX_PAGE, $page));
        $offset = ($page - 1) * Listing::PER_PAGE;
        return $this->_listAll(Listing::PER_PAGE, $offset, $user_id);
    }

    public function get(int $id, int $user_id): ?array
    {
        if (!$id || !$user_id) {
            throw new \InvalidArgumentException('Not enough arguments');
        }

        return $this->_findByID($id, $user_id);
    }

    public function getCovers(int $id): ?array
    {
        if (!$id) {
            throw new \InvalidArgumentException('Not enough arguments');
        }
        return $this->_listCoversByID($id);
    }

    public function listByUser(int $user_id): array
    {
        return $this->_listByUser($user_id);
    }

    /**
     * @param array<int,mixed> $images
     */
    public function create(string $title, string $price, string $description, array $images): void
    {
        if (!$title || !$price || !$description) {
            throw new \InvalidArgumentException('Not enough arguments');
        }

        if (strlen($title) < Listing::MIN_TITLE_LEN || strlen($title) > Listing::MAX_TITLE_LEN) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Nieprawidłowa długość tytułu; musi być pomiędzy %d a %d',
                    Listing::MIN_TITLE_LEN,
                    Listing::MAX_TITLE_LEN,
                ),
                1,
            );
        }

        if (strlen($description) < Listing::MIN_DESC_LEN || strlen($description) > Listing::MAX_DESC_LEN) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Nieprawidłowa długość opisu; musi być pomiędzy %d a %d',
                    Listing::MIN_DESC_LEN,
                    Listing::MAX_DESC_LEN,
                ),
                1,
            );
        }

        if (!preg_match('/^\d{,4}(?:(?:,|\.)\d\d)?$/', $price)) {
            throw new \InvalidArgumentException('Nieprawidłowy format ceny', 1);
        }

        $parsed_price = str_replace('.', ',', $price);

        $id = $this->_create($_SESSION['user_id'], $title, $parsed_price, $description);

        if (!$id) {
            throw new \ErrorException('DB error');
        }

        $res = $this->_addCovers($id, $images);

        if (!$res) {
            throw new \InvalidArgumentException('Failed to add cover');
        }
    }
}
