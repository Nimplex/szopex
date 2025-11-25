<?php

use App\FlashMessage;

/** @var \App\Controller\UserController $user */
global $user, $_ROUTE;

try {
    $user->activate_from_request($_ROUTE);
    (new FlashMessage())->setOk('Konto zostało aktywowane!');
    header('Location: /login', true, 303);
} catch (\InvalidArgumentException $e) {
    (new FlashMessage())->fromException($e);
    header('Location: /', true, 303);
}
