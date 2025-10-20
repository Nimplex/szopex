<?php

/** @var PDO $db */
require __DIR__ . '/../bootstrap.php';

$sql = <<<SQL
ALTER TABLE users ADD UNIQUE (login); 
SQL;

$db->exec($sql);

echo "Migration 008 applied\n";
