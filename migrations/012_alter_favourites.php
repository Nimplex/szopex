<?php

/** @var PDO $db */
require __DIR__ . '/../bootstrap.php';

$sql = <<<SQL
ALTER TABLE favourites ADD CONSTRAINT favourites_user_listing_unique UNIQUE (user_id, listing_id);
CREATE INDEX idx_favourites_user_id ON favourites(user_id);
CREATE INDEX idx_favourites_listing_user ON favourites(listing_id, user_id);
SQL;

$db->exec($sql);

echo "Migration 012 applied\n";
