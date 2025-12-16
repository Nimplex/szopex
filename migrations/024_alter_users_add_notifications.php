<?php

/** @var PDO $db */
require __DIR__ . '/../bootstrap.php';

$sql = <<<SQL
ALTER TABLE users
ADD COLUMN notifications_message BOOLEAN DEFAULT TRUE,
ADD COLUMN notifications_reports BOOLEAN DEFAULT TRUE,
ADD COLUMN notifications_login BOOLEAN DEFAULT TRUE,
ADD COLUMN notifications_listings BOOLEAN DEFAULT TRUE,
ADD COLUMN notifications_administrative BOOLEAN DEFAULT TRUE,
ADD COLUMN notifications_contact BOOLEAN DEFAULT TRUE,
ADD COLUMN notifications_marketing BOOLEAN DEFAULT TRUE,
ADD COLUMN mobile_app_notifications BOOLEAN DEFAULT TRUE,
ADD COLUMN email_notifications BOOLEAN DEFAULT TRUE;
SQL;

$db->exec($sql);

echo "Migration 024 applied\n";
