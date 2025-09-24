<?php

require __DIR__ . '/../bootstrap.php';

$sql = <<<SQL
CREATE TABLE items (
    id SERIAL PRIMARY KEY,
    listing_id INT NOT NULL REFERENCES listings(id) ON DELETE CASCADE,
    title VARCHAR(100) NOT NULL,
    attributes JSONB,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
SQL;

$db->exec($sql);

echo "Migration 003 applied\n";
