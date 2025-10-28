<?php

require $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';

$msg = new App\FlashMessage();

[
    'title' => $title,
    'price' => $price,
    'description' => $description,
] = $_POST;

$listing = (new App\Builder\ListingBuilder())->make();

try {
    // $listing->create($title, $price, $description, $_FILES);
    // $msg->setOk('Utworzono nową ofertę');
    // header('Location: /listings/my-listings.php', true, 303);

    $listing->balls($_FILES['images']);
} catch (\InvalidArgumentException $e) {
    $msg->fromException($e);
    header('Location: /listings/new.php', true, 303);
}
