<?php

if (isset($_SERVER['HTTP_PARTIAL_REQ'])) {
    require $_SERVER['DOCUMENT_ROOT'] . '/../resources/components/templates/listings.php';
    die;
}

$lis = (new App\Builder\ListingBuilder())->make();

$TITLE = 'Ogłoszenia';
$HEAD = <<<HTML
<link rel="stylesheet" href="/_dist/css/all_listings.css">
<noscript>
    <style>
        #throbber { display: none; }
    </style>
</noscript>
HTML;
$SCRIPTS = [
    '/_dist/js/listings.js',
    '/_dist/js/scroll.js',
];

$total_pages = $lis->countPages();

ob_start();
?>

<div id="heading">
    <h1>Aktualne ogłoszenia</h1>
    <form action="/listings/new" method="GET">
        <button class="btn-accent" type="submit">
            <i data-lucide="package-plus" aria-hidden="true"></i>
            Nowe ogłoszenie
        </button>
    </form>
</div>
<hr>
<div id="offers">
    <?php require $_SERVER['DOCUMENT_ROOT'] . '/../resources/components/templates/listings.php'; ?>
</div>

<?php
$CONTENT = ob_get_clean();

require $_SERVER['DOCUMENT_ROOT'] . '/../resources/components/container.php';
