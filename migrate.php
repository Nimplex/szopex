<?php

require __DIR__ . '/bootstrap.php';

$db->exec('
    CREATE TABLE IF NOT EXISTS migrations (
        id SERIAL PRIMARY KEY,
        filename VARCHAR(255) NOT NULL,
        applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
');

$applied = $db->query('SELECT filename FROM migrations')
              ->fetchAll(PDO::FETCH_COLUMN);

$files = glob(__DIR__ . '/migrations/*.php');
sort($files);

foreach ($files as $file) {
    $filename = basename($file);

    if (in_array($filename, $applied)) {
        continue;
    }

    echo "Applying $filename...\n";
    require $file;

    $stmt = $db->prepare('INSERT INTO migrations (filename) VALUES (:filename)');
    $stmt->execute(['filename' => $filename]);
}

echo "Migrations up to date.\n";

