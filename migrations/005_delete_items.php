<?php

/** @var PDO $db */
require __DIR__ . '/../bootstrap.php';

$sql = <<<SQL
ALTER TABLE listings ADD attributes JSONB;
UPDATE listings t SET attributes = s.attributes FROM items s WHERE t.id = s.listing_id;
DROP TABLE items;
SQL;

$db->exec($sql);

echo "Migration 005 applied\n";
