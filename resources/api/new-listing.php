<?php

[
    'title' => $title,
    'price' => $price,
    'description' => $description,
] = $_POST;

require $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';
$listing = (new App\Builder\ListingBuilder())->make();
$status = $listing->create($title, $price, $description);

// send '303 See Other' to redirect to page
header('Location: /listings/my-listings' . ($status == 0 ? "?ok=1" : "/new?err=$status"), true, 303);
