<?php

use App\FlashMessage;

/** @var \App\Controller\UserController $user */
global $user;

try {
    $user->register_from_request($_POST);
    (new FlashMessage())->setOk('Rejestracja udana! Wysłano kod aktywacyjny na skrzynke e-mail');
    header('Location: /login', true, 303);
} catch (\InvalidArgumentException $e) {
    (new FlashMessage())->fromException($e);
    header('Location: /register', true, 303);
}
