<?php

/** @var \App\Controller\UserController $user */
global $user, $_ROUTE;

$listing_model = (new App\Builder\ListingBuilder())->make();
$chats_model = (new App\Builder\ChatsBuilder())->make();

$current_user_id = $_SESSION['user_id'];
$content = $_POST['content'] ?: null;
$listing_id = $_POST['listing_id'] ?: null;
$user_id = $_POST['user_id'] ?: null;

$listing = null;

echo '<br>';
if ((!isset($listing_id) && !isset($user_id)) || !isset($content)) {
    header('Location: /messages', true, 303);
    die;
}

if (strlen($content) == 0) {
    // todo: proper handling
    header('Location: /messages', true, 303);
    die;
}

if (isset($listing_id)) {
    $listing = $listing_model->get($listing_id, -1);
    if (!$listing) {
        // todo: error handling, inform user
        header('Location: /messages', true, 303);
        die;
    }
}

if (isset($user_id)) {
    $exists = $user->user->exists($user_id);
    if (!$exists) {
        // todo: error handling, inform user
        header('Location: /messages', true, 303);
        die;
    }
}

$seller_id = $listing_id ? $listing['user_id'] : $user_id;

if ($listing_id) {
    $res = $chats_model->find_listings($seller_id, $current_user_id, $listing_id);
    if ($res) {
        header("Location: /messages/{$res['id']}", true, 303);
        die;
    }
} else {
    $res = $chats_model->find_standalone($seller_id, $current_user_id);
    if ($res) {
        header("Location: /messages/{$res['id']}", true, 303);
        die;
    }
}

$chat_id = $chats_model->create($seller_id, $current_user_id, $listing_id);

// == false because ! will also match ID 0
if ($chat_id == false) {
    // todo: proper error handling, inform user about database error
    header('Location: /messages', true, 303);
    die;
}

$message_id = $chats_model->add_message($chat_id, $current_user_id, $content);

header("Location: /messages/{$chat_id}", true, 303);
