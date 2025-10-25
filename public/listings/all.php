<?php

require $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';
require $_SERVER['DOCUMENT_ROOT'] . '/../resources/check-auth.php';

if (isset($_SERVER['HTTP_HX_REQUEST'])) {
    require $_SERVER['DOCUMENT_ROOT'] . '/../resources/hx-templates/listings.php';
    die;
}

$listingBuilder = (new App\Builder\ListingBuilder())->make();

$title = 'Ogłoszenia';
$current_page = (int)($_GET['page'] ?? 0);

$render_head = function () {
    return <<<HTML
    <link rel="stylesheet" href="/_css/all_listings.css">
    <noscript>
        <style>
            #throbber { display: none; }
        </style>
    </noscript>
    HTML;
};

$render_content = function () {
    global $listingBuilder, $current_page;
    $total_pages = $listingBuilder->countPages();

    ob_start();
    require $_SERVER['DOCUMENT_ROOT'] . '/../resources/hx-templates/listings.php';
    $listings = ob_get_clean();

    return <<<HTML
    <h1>Aktualne ogłoszenia</h1>
    <hr>
    <div id="offers">
        $listings
        <div id="throbber" aria-hidden="true" class="htmx-indicator small-text">Wczytywanie...</span>
    </div>
    HTML;
};

$render_scripts = function () {
    return <<<HTML
    <script src="/_js/htmx.min.js"></script>
    HTML;
};

require $_SERVER['DOCUMENT_ROOT'] . '/../resources/components/container.php';
