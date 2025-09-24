<?php

require __DIR__ . '/../bootstrap.php';

$sql = <<<SQL
CREATE TABLE listings (
    id SERIAL PRIMARY KEY,
    user_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    title VARCHAR(100) NOT NULL,
    price money NOT NULL,
    description VARCHAR(1000) NOT NULL,
    status boolean NOT NULL DEFAULT TRUE,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);
SQL;

$db->exec($sql);

echo "Migration 002 applied\n";

