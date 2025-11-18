<?php

require $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';

/** @var \App\Controller\UserController $user */
global $user;

try {
    $user->favourite_from_request($_POST);
} catch (\InvalidArgumentException $e) {
    http_response_code(500);
}
