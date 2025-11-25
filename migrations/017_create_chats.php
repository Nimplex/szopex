<?php

/** @var PDO $db */
require __DIR__ . '/../bootstrap.php';

// hidden - 1 - seller - 2 - user -- 3 all
$sql = <<<SQL
CREATE TABLE chats (
    id SERIAL PRIMARY KEY,
    seller_id INT NOT NULL REFERENCES users(id),
    buyer_id INT NOT NULL REFERENCES users(id),
    hidden INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT unique_pair UNIQUE (seller_id, buyer_id)
);
SQL;

$db->exec($sql);

echo "Migration 017 applied\n";
