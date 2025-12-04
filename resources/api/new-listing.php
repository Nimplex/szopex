<?php

$msg = new App\FlashMessage();

$title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS);
$price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
$description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_SPECIAL_CHARS);

if (!isset($title) || !isset($price) || !isset($description)) {
    $msg->setErr('i18n:invalid_query');
    header('Location: /listings/new', true, 303);
    die;
}

$listing = (new App\Builder\ListingBuilder())->make();

try {
    $listing->create($title, $price, $description, $_FILES);
    $msg->setOk('i18n:offer_created');
    header('Location: /profile/listings', true, 303);
} catch (\InvalidArgumentException $e) {
    $msg->fromException($e);
    header('Location: /listings/new', true, 303);
}
