<?php

require $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';
require $_SERVER['DOCUMENT_ROOT'] . '/../resources/check-auth.php';

if (isset($_SERVER['HTTP_HX_REQUEST'])) {
    require $_SERVER['DOCUMENT_ROOT'] . '/../resources/hx-templates/listings.php';
    die;
}

$listingBuilder = (new App\Builder\ListingBuilder())->make();

$title = 'Ogłoszenia';
$current_page = (int)$_GET['page'] ?? 0;

function render_head()
{
    return <<<HTML
    <link rel="stylesheet" href="/_css/all_listings.css">
    HTML;
}

function render_content()
{
    global $listingBuilder, $current_page;
    $total_pages = $listingBuilder->countPages();

    ob_start();
    require $_SERVER['DOCUMENT_ROOT'] . '/../resources/hx-templates/listings.php';
    $listings = ob_get_clean();


    $pages_navigation = '<nav aria-label="Nawigacja po stronach"><ul>';

    if ($current_page > 1) {
        $pages_navigation .= "<li><a href=\"?page=1\">1</a></li>";
    }

    if ($current_page - 1 > 1) {
        $pages_navigation .= "<li><a href=\"?page=" . ($current_page - 1) . "\">" . ($current_page - 1) . "</a></li>";
    }

    $pages_navigation .= "<li aria-current=\"page\">$current_page</li>";

    if ($current_page + 1 < $total_pages) {
        $pages_navigation .= "<li><a href=\"?page=" . ($current_page + 1) . "\">" . ($current_page + 1) . "</a></li>";
    }

    if ($current_page < $total_pages) {
        $pages_navigation .= "<li><a href=\"?page=$total_pages\">$total_pages</a></li>";
    }

    $pages_navigation .= '</ul></nav>';

    return <<<HTML
    <h1>Aktualne ogłoszenia</h1>
    <hr>
    <div id="offers">
        $listings
        <!-- this is just a placeholder, later we can put something else in here -->
        <div id="throbber" class="htmx-indicator small-text">Wczytywanie...</span>
    </div>
    <noscript>
        $pages_navigation
    </noscript>
    HTML;
}

function render_scripts()
{
    return <<<HTML
    <script src="/_js/htmx.min.js"></script>
    HTML;
}

require $_SERVER['DOCUMENT_ROOT'] . '/../resources/components/container.php';
