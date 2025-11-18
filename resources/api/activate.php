<?php

require $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';
use App\FlashMessage;

/** @var \App\Controller\UserController $user */
global $user;
global $_ROUTE;

try {
    $user->activate_from_request($_ROUTE);
    (new FlashMessage())->setOk('Konto zostało aktywowane!');
    header('Location: /login.php', true, 303);
} catch (\InvalidArgumentException $e) {
    (new FlashMessage())->fromException($e);
    header('Location: /', true, 303);
}
