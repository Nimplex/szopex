<?php

/** @var PDO $db */
require __DIR__ . '/../bootstrap.php';

$sql = <<<SQL
ALTER TABLE users ADD COLUMN role VARCHAR(255);
SQL;

$db->exec($sql);

echo "Migration 025 applied\n";
