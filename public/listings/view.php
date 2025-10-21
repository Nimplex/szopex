<?php
require $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';

$listing_id = $_GET['listing'];

$listingBuilder = (new App\Builder\ListingBuilder())->make();

$listing = $listingBuilder->get($listing_id);
$title = $listing['title'];

function render_head(): string
{
    return <<<HTML
    <link rel="stylesheet" href="/_css/view.css">
    HTML;
}

function render_content(): string {
    global $title;
    return <<<HTML
    <h1>{$title}</h1>
    HTML;
}


require $_SERVER['DOCUMENT_ROOT'] . '/../resources/components/container.php';
