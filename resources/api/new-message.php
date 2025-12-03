<?php

/** @var \App\Controller\UserController $user */
global $user;

$current_user_id = $_SESSION['user_id'];
$content = $_POST['content'] ?: null;
$listing_id = $_POST['listing_id'] ?: null;
$user_id = $_POST['user_id'] ?: null;

if (empty($content)) {
    die('Content is required.');
}

if (empty($listing_id) || !is_numeric($listing_id)) {
    die('Invalid listing ID.');
}

if (empty($user_id) || !is_numeric($user_id)) {
    die('Invalid user ID.');
}
