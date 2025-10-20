<?php

require $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';
use App\FlashMessage;

/** @var \App\Model\Auth $auth */
global $auth;

try {
    $auth->register_from_request($_POST);
    (new FlashMessage())->setOk('Rejestracja udana!');
    header('Location: /login.php', true, 303);
} catch (\InvalidArgumentException $e) {
    (new FlashMessage())->fromException($e);
    header('Location: /register.php', true, 303);
}
