<?php

$msg = new App\FlashMessage();

[
    'title' => $title,
    'price' => $price,
    'description' => $description,
] = $_POST;

$listing = (new App\Builder\ListingBuilder())->make();

try {
    $listing->create($title, $price, $description, $_FILES);
    $msg->setOk('Utworzono nową ofertę');
    header('Location: /profile/listings', true, 303);
} catch (\InvalidArgumentException $e) {
    $msg->fromException($e);
    header('Location: /listings/new', true, 303);
}
