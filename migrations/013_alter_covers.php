<?php

/** @var PDO $db */
require __DIR__ . '/../bootstrap.php';

$sql = <<<SQL
CREATE INDEX idx_covers_listing_main ON covers(listing_id, main) WHERE main = TRUE;
SQL;

$db->exec($sql);

echo "Migration 014 applied\n";
