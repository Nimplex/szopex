<?php

/** @var PDO $db */
require __DIR__ . '/../bootstrap.php';

$sql = <<<SQL
CREATE INDEX idx_users_id_display_name ON users(id, display_name);
CREATE INDEX idx_users_active ON users(active) WHERE active = TRUE;
SQL;

$db->exec($sql);

echo "Migration 015 applied\n";
