<?php

/** @var PDO $db */
require __DIR__ . '/../bootstrap.php';

$sql = <<<SQL
CREATE TABLE covers (
    id SERIAL PRIMARY KEY,
    file_id VARCHAR(255) NOT NULL,
    listing_id INT NOT NULL REFERENCES listings(id) ON DELETE CASCADE,
    main boolean NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
SQL;

$db->exec($sql);

echo "Migration 006 applied\n";
