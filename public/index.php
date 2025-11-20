<?php

session_start();

/** @var PDO $db */
require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/../vendor/autoload.php';

use App\Controller\UserController;

$user = new UserController($db);
$router = new App\Router();

$router->GET("/", function () {
    header('Location: /listings/all.php', true, 303);
    die;
    echo 'home page???<br>';
    echo 'is logged in: ' . (isset($_SESSION['user_id']) ? 'true<br><a href="/api/logout">Logout</a>' : 'false');
});


$router->GET('/api/logout', function () {
    session_destroy();
    header('Location: /?logout=1', true, 303);
    die;
});

$router->GET('/api/activate/:id/:token', fn () => require __DIR__ . '/../resources/api/activate.php');

$router->POST('/api/register', fn () => require __DIR__ . '/../resources/api/register.php');
$router->POST('/api/login', fn () => require __DIR__ . '/../resources/api/login.php');
$router->POST('/api/new-listing', fn () => require __DIR__ . '/../resources/api/new-listing.php');
$router->POST('/api/listings/favourite', fn () => require __DIR__ . '/../resources/api/favourites.php');

$router->DEFAULT(function () {
    require __DIR__ . '/404.php';
    die;
});

$router->handle();
