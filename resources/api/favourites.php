<?php

require $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';

/** @var \App\Controller\UserController $user */
global $user;

try {
    echo $user->favourite_from_request($_POST) ? "yes" : "no";
} catch (\InvalidArgumentException $e) {
    http_response_code(500);
}
