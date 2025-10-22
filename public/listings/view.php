<?php

require $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';
require $_SERVER['DOCUMENT_ROOT'] . '/../src/ISO3166.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/../src/WebpackManifest.php';
use App\WebpackManifest;


$listing_id = $_GET['listing'];

$listingBuilder = (new App\Builder\ListingBuilder())->make();

$listing = $listingBuilder->get($listing_id);
$listing_covers = $listingBuilder->getCovers($listing_id);

$title = htmlspecialchars($listing['title']);

$key_lookup_table = [
    'isbn' => 'ISBN',
    'cover' => 'Okładka',
    'genre' => 'Gatunek',
    'pages' => 'Strony',
    'title' => 'Tytuł',
    'author' => 'Autor',
    'release' => 'Data wydania',
    'language' => 'Język',
    'condition' => 'Stan',
    'publisher' => 'Wydawca',
    'subject' => 'Przedmiot',
];

function render_head(): string
{
    $style_path = WebpackManifest::asset('view.css');
    return <<<HTML
    <link rel="stylesheet" href="{$style_path}">
    HTML;
}

function render_content(): string
{
    global $title, $listing, $listing_covers, $key_lookup_table;

    $array_size = count($listing_covers);
    $disabled = $array_size <= 1 ? "disabled" : null;
    $main_cover = "";
    $carousel = "<ul>";
    $attributes = "";
    $description = htmlspecialchars($listing['description']);

    foreach ($listing_covers as $cover) {
        if ($cover['main'] == true) {
            $main_cover = $cover['file_id'];
        }

        $carousel .= <<<HTML
        <li>
            <img src="/covers.php?file={$cover['file_id']}" alt="Podgląd '{$title}'">
        </li>
        HTML;
    }

    $carousel .= "</ul>";

    foreach (json_decode($listing['attributes']) as $attribute => $value) {
        $attribute = htmlspecialchars($attribute);
        $value = htmlspecialchars($value);

        switch ($attribute) {
        case 'language':
                $language = iso3_to_language($value);
                $value = "{$language['normalized']} ({$language['localized']})";
                break;
        }

        $attributes .= <<<HTML
        <tr>
            <th class="with-icon"><i data-lucide="table-properties" aria-hidden="true"></i>{$key_lookup_table[$attribute]}</th>
            <td>{$value}</td>
        </tr>
        HTML;
    }

    $carousel_section = ($array_size == 0) ? null : <<<HTML
        <section class="carousel" role="region" aria-roledescription="carousel" aria-label="Zdjęcia oferty">
            <div id="cover-container">
                <img src="/covers.php?file={$main_cover}" id="main-cover">
                <button class="left" aria-label="Poprzednie zdjęcie" $disabled>‹</button>
                <button class="right" aria-label="Kolejne zdjęcie" $disabled>›</button>
            </div>
            <hr>
            {$carousel}
        </section>
    HTML;

    $template = <<<HTML
    <div class="row">
        {$carousel_section}
        <div id="inner-container">
            <h1>{$title}</h1>
            <hr>
            <div class="row">
                <div id="data-section">
                    <div>
                        <p class="with-icon"><i data-lucide="book-open-text" aria-hidden="true"></i>Opis:</p>
                        <p>{$description}</p>
                    </div>
                    <table id="details">
                        <tbody>
                           {$attributes}
                        </tbody>
                    </table>
                </div>
                <div id="button-section">
                    <h1>{$listing['price']}</h1>
                    <button>Dodaj do ulubionych</button>
                    <button class="btn-accent">Napisz do sprzedawcy</button>
                    <button class="btn-no-border-red"><i data-lucide="flag"></i>Zgłoś</button>
                </div>
            </div>
        </div>
    </div>
    HTML;

    return $template;
}

function render_scripts()
{
    return <<<HTML
    <script src="/_js/carousel.js"></script>
    HTML;
}

require $_SERVER['DOCUMENT_ROOT'] . '/../resources/components/container.php';
