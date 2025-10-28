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
        $stmt = $this->db->prepare(<<<SQL
        SELECT
            l.id AS listing_id,
            l.user_id,
            l.title,
            l.price,
            l.created_at,
            l.attributes,
            u.display_name,
            c.file_id AS cover_file_id
        FROM listings l
        LEFT JOIN users u
            ON u.id = l.user_id
        LEFT JOIN covers c
            ON c.listing_id = l.id
            AND c.main = TRUE
        ORDER BY l.created_at DESC
        LIMIT :limit OFFSET :offset
        SQL);       // I'm using bindValue to enforce use of INT
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
   
    private function _findByID(int $id): ?array
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
           c.file_id AS cover_file_id,
           u.display_name
        FROM listings l
        LEFT JOIN users u
            ON u.id = l.user_id
        LEFT JOIN covers c
            ON c.listing_id = l.id
        WHERE l.id = ?
        SQL);
        $stmt->execute([$id]);
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
        $stmt = $this->db->prepare('SELECT * FROM listings WHERE user_id = ?');
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
     * @param array<string,array<int,mixed>> $files
     */
    private function _addCovers(int $listing_id, array $files): bool
    {
        // temporary; i'll finish this later
        return false;
        
        if (!isset($files) || empty($files)) {
            return false;
        }

        foreach ($files['error'] as $i => $err) {
            switch ((int) $err) {
                case UPLOAD_ERR_OK:
                    break;

                case UPLOAD_ERR_NO_FILE:
                case UPLOAD_ERR_PARTIAL:
                    // for whatever reason in PHP `switch` is considered a loop,
                    // so we break the 2nd "loop" (foreach)
                    continue 2;

                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    throw new \InvalidArgumentException('Plik jest zbyt duży', 1);

                default: // this should basically never happen
                    error_log('File upload warning: ' . (int) $err);
                    // same as above
                    continue 2;
            }

            $tmp_name = $files['tmp_name'][$i];
            $mime = $files['type'][$i];

            if (mime_content_type($tmp_name) != $mime) {
                error_log("File upload warning: MIME types don't match");
                continue;
            }

            $size = $files['size'][$i];

            $file_extension = match ($mime) {
                'image/jpeg' => '.jpg',
                'image/png' => '.png',
            };

            do {
                $new_name = sprintf('%d-%s', $listing_id, bin2hex(random_bytes(16)));
                $final_file_path = $_SERVER['DOCUMENT_ROOT'] . '/../storage/covers/' . $new_name . $file_extension;
            } while (file_exists($final_file_path));
            
            move_uploaded_file($tmp_name, $final_file_path);

            
        }

        return true;
    }

    // --- public methods ---

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

    public function listAll(int $page): array
    {
        $page = max(Listing::MIN_PAGE, min(Listing::MAX_PAGE, $page));
        $offset = ($page - 1) * Listing::PER_PAGE;
        return $this->_listAll(Listing::PER_PAGE, $offset);
    }

    public function get(int $id): ?array
    {
        if (!$id) {
            throw new \InvalidArgumentException('Not enough arguments');
        }
        return $this->_findByID($id);
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
    // TODO: finish recieving files
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

        // WIP
        // $res = $this->_addCovers($id, $images);
    }
}
