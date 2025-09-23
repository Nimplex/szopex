<?php
session_start();

require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/../vendor/autoload.php';

use App\Model\User;
use App\Service\AuthService;

$userModel = new User($db);
$authService = new AuthService($userModel);

$path = $_SERVER['PATH_INFO'] ?? '/';
$method = $_SERVER['REQUEST_METHOD'];

if ($path === '/register' && $method === 'POST') {
    $result = $authService->register($_POST['username'], $_POST['email'], $_POST['password']);
    echo json_encode(['success'=>$result]);
} elseif ($path === '/login' && $method === 'POST') {
    $result = $authService->login($_POST['email'], $_POST['password']);
    echo json_encode(['success'=>$result]);
} else {
    http_response_code(404);
    echo 'Not found';
}
