<?php

require $_SERVER['DOCUMENT_ROOT'] . '/../resources/check-auth.php';

if (isset($_SERVER['HTTP_HX_REQUEST'])) {
    require $_SERVER['DOCUMENT_ROOT'] . '/../resources/hx-templates/listings.php';
    die;
}

$title = 'Oferty';

function render_head()
{
    return <<<HTML
    <link rel="stylesheet" href="/_css/all_listings.css">
    HTML;
}

function render_content()
{
    ob_start();
    require $_SERVER['DOCUMENT_ROOT'] . '/../resources/hx-templates/listings.php';
    $listings = ob_get_clean();
    
    return <<<HTML
    <h1>Aktualne oferty</h1>
    <div id="offers">
        $listings
        <!-- this is just a placeholder, later we can put something else in here -->
        <span id="throbber" class="htmx-indicator">Loading...</span>
    </div>
    HTML;
}

function render_scripts()
{
    return <<<HTML
    <script src="/_js/htmx.min.js"></script>
    HTML;
}

require $_SERVER['DOCUMENT_ROOT'] . '/../resources/components/container.php';
