<?php

use App\FlashMessage;

/** @var \App\Controller\UserController $user_controller */
global $user_controller;

$target = $_POST['redir'] ?? '/';
// echo '<pre>' . var_dump(str_split($target), '') . '</pre>';
// die;

if (!preg_match('/^[\w\/]+(?:\?.*)?$/', $target)) {
    $target = '/';
}

try {
    $user_controller->login_from_request($_POST);
    header("Location: {$target}", true, 303);
} catch (\InvalidArgumentException $e) {
    (new FlashMessage())->fromException($e);
    $uri = urlencode($_POST['redir']);
    header("Location: /login?redir={$uri}", true, 303);
}
