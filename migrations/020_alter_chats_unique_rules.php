<?php

/** @var PDO $db */
require __DIR__ . '/../bootstrap.php';

// Drop old constraint
$db->exec("
    ALTER TABLE chats
    DROP CONSTRAINT IF EXISTS unique_pair;
");


$db->exec("
    CREATE UNIQUE INDEX IF NOT EXISTS chats_unique_direct
    ON chats (seller_id, buyer_id)
    WHERE listing_id IS NULL;
");

$db->exec("
    CREATE UNIQUE INDEX IF NOT EXISTS chats_unique_listing
    ON chats (seller_id, buyer_id, listing_id)
    WHERE listing_id IS NOT NULL;
");

echo "Migration 20 applied\n";

