<?php

session_start();

/** @var PDO $db */
require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/../vendor/autoload.php';

use App\Model\Auth;
use App\Model\Listing;

$auth = new Auth($db);
$listing = new Listing($db);

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
} elseif ($path === '/api/listings' && $method === 'GET') {
    $res = $listing->listAll($_GET);
    echo $res;
} else {
    require __DIR__ . '/404.php';
    die;
}
