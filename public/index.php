<?php

session_start();

/** @var PDO $db */
require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/../vendor/autoload.php';

use App\Model\Auth;

$auth = new Auth($db);

$path = $_SERVER['PATH_INFO'] ?? '/';
$method = $_SERVER['REQUEST_METHOD'];

if ($path === "/") {
    echo 'home page???<br>';
    echo 'is logged in: ' . (isset($_SESSION['user_id']) ? 'true' : 'false');
} elseif ($path === '/register' && $method === 'POST') {
    $res = $auth->register_from_request($_POST);
    echo $res;
} elseif ($path === '/login' && $method === 'POST') {
    $res = $auth->login_from_request($_POST);
    echo $res;
} elseif ($path === '/logout') {
    session_destroy();
    echo 'Logged out';
} elseif (substr($path, 0, 5) === '/api/') {
    require __DIR__ . match ([$path, $method]) {
        ['/api/login', 'POST'] => '/../resources/api/login.php',
        ['/api/new-listing', 'POST'] => '/../resources/api/new-listing.php',
        default => '/404.php',
    };
    die;
} else {
    require __DIR__ . '/404.php';
    die;
}
