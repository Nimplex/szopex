<?php

if (isset($_SERVER['HTTP_RAW_REQUEST'])) {
    require $_SERVER['DOCUMENT_ROOT'] . '/../resources/hx-templates/listings.php';
    die;
}

$lis = (new App\Builder\ListingBuilder())->make();

$title = 'Ogłoszenia';

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

$render_content = function () use ($lis) {
    $total_pages = $lis->countPages();

    ob_start();
    require $_SERVER['DOCUMENT_ROOT'] . '/../resources/hx-templates/listings.php';
    $listings = ob_get_clean();

    return <<<HTML
    <h1>Aktualne ogłoszenia</h1>
    <hr>
    <div id="offers">$listings</div>
    HTML;
};

$render_scripts = function () {
    return <<<HTML
    <script src="/_js/listings.js"></script>
    <script src="/_js/scroll.js"></script>
    HTML;
};

require $_SERVER['DOCUMENT_ROOT'] . '/../resources/components/container.php';
