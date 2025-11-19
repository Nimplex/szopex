<?php

/** @var PDO $db */
require __DIR__ . '/../bootstrap.php';

$sql = <<<SQL
CREATE INDEX idx_listings_created_at ON listings(created_at);
CREATE INDEX idx_listings_user_id ON listings(user_id);
SQL;

$db->exec($sql);

echo "Migration 013 applied\n";
