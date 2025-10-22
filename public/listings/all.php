<?php

require $_SERVER['DOCUMENT_ROOT'] . '/../resources/check-auth.php';

if (isset($_SERVER['HTTP_HX_REQUEST'])) {
    require $_SERVER['DOCUMENT_ROOT'] . '/../resources/hx-templates/listings.php';
    die;
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/../src/WebpackManifest.php';
use App\WebpackManifest;

$title = 'Ogłoszenia';

function render_head()
{
    $style_path = WebpackManifest::asset('all_listings.css');
    return <<<HTML
    <link rel="stylesheet" href="{$style_path}">
    HTML;
}

function render_content()
{
    ob_start();
    require $_SERVER['DOCUMENT_ROOT'] . '/../resources/hx-templates/listings.php';
    $listings = ob_get_clean();
    
    return <<<HTML
    <h1>Aktualne ogłoszenia</h1>
    <hr>
    <div id="offers">
        $listings
        <!-- this is just a placeholder, later we can put something else in here -->
        <div id="throbber" class="htmx-indicator">Wczytywanie...</span>
    </div>
    HTML;
}

require $_SERVER['DOCUMENT_ROOT'] . '/../resources/components/container.php';
