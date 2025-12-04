<?php

$msg = new App\FlashMessage();

/** @var \App\Controller\UserController $user_controller */
global $user_controller;

try {
    $user_controller->add_profile_picture($_SESSION['user_id'], $_FILES['pfp']);
    $msg->setOk('Pomyślnie ustawiono zdjęcie profilowe');
    header('Location: /profile', true, 303);
} catch (\InvalidArgumentException $e) {
    $msg->fromException($e);
    header('Location: /listings/new', true, 303);
}
