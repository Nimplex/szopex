<?php

/** @var PDO $db */
require __DIR__ . '/../bootstrap.php';

$sql = <<<SQL
ALTER TABLE user_reports
ADD COLUMN reason VARCHAR(255);
SQL;

$db->exec($sql);

echo "Migration 023 applied\n";
