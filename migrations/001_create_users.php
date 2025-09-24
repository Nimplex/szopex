<?php

require __DIR__ . '/../bootstrap.php';

$sql = <<<SQL
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    login VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
SQL;

$db->exec($sql);

echo "Migration 001 applied\n";

