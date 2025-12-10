<?php

/** @var PDO $db */
require __DIR__ . '/../bootstrap.php';

$sql = <<<SQL
CREATE TABLE user_reports (
    id SERIAL PRIMARY KEY,
    reporter_id INT NOT NULL REFERENCES users(id),
    reported_id INT NOT NULL REFERENCES users(id),
    listing_id INT REFERNCES listings(id),
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
);
SQL;

$db->exec($sql);

echo "Migration 022 applied\n";
