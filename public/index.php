<?php

session_start();

/** @var PDO $db */
require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/../vendor/autoload.php';

use App\Controller\AuthController;
use App\Model\User;
use App\Service\AuthService;

use App\Controller\ListingController;
use App\Model\Listing;
use App\Service\ListingService;

$userModel = new User($db);
$authService = new AuthService($userModel);
$authController = new AuthController($authService);

$listingModel = new Listing($db);
$listingService = new ListingService($listingModel);
$listingController = new ListingController($listingService);

$path = $_SERVER['PATH_INFO'] ?? '/';
$method = $_SERVER['REQUEST_METHOD'];

if ($path === '/register' && $method === 'POST') {
    $res = $authController->register($_POST);
    echo $res;
} elseif ($path === '/login' && $method === 'POST') {
    $res = $authController->login($_POST);
    echo $res;
} elseif ($path === '/logout') {
    session_destroy();
    echo 'Logged out';
} elseif ($path === '/listings' && $method === 'GET') {
    $res = $listingController->listAll($_GET);
    echo $res;
} else {
    header('Location: /404.php');
    die;
}
