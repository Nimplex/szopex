<?php

use App\FlashMessage;

/** @var \App\Controller\UserController $user */
global $user;

try {
    $user->login_from_request($_POST);
    header('Location: /?login=1', true, 303);
} catch (\InvalidArgumentException $e) {
    (new FlashMessage())->fromException($e);
    header('Location: /login', true, 303);
}
