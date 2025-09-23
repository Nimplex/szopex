<?php
require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$params = [
    'host' => $_ENV['DB_HOST'],
    'port' => (int)$_ENV['DB_PORT'],
    'database' => $_ENV['DB_NAME'],
    'user' => $_ENV['DB_USER'],
    'password' => $_ENV['DB_PASS'],
];

$dbInfo = sprintf(
    "pgsql:host=%s;port=%d;dbname=%s",
    $params['host'],
    $params['port'],
    $params['database']
);

$db = new PDO($dbInfo, $params['user'], $params['password']);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
