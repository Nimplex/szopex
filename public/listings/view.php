<?php

require $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';

$iso = new Matriphe\ISO639\ISO639();
$listingBuilder = (new App\Builder\ListingBuilder())->make();

$listing_id = $_GET['listing'];
$listing = $listingBuilder->get($listing_id);
$listing_covers = $listingBuilder->getCovers($listing_id);

$title = htmlspecialchars($listing['title']);

$key_lookup_table = [
    'isbn' => [
        'display' => 'ISBN',
        'icon' => 'barcode',
    ],
    'cover' => [
        'display' => 'Okładka',
        'icon' => 'book',
    ],
    'genre' => [
        'display' => 'Gatunek',
        'icon' => 'drama',
    ],
    'pages' => [
        'display' => 'Strony',
        'icon' => 'book-open-text',
    ],
    'title' => [
        'display' => 'Tytuł',
        'icon' => 'case-sensitive',
    ],
    'author' => [
        'display' => 'Autor',
        'icon' => 'signature',
    ],
    'release' => [
        'display' => 'Data wydania',
        'icon' => 'table-properties',
    ],
    'language' => [
        'display' => 'Język',
        'icon' => 'languages',
    ],
    'condition' => [
        'display' => 'Stan',
        'icon' => 'brush-cleaning',
    ],
    'publisher' => [
        'display' => 'Wydawca',
        'icon' => 'building',
    ],
    'subject' => [
        'display' => 'Przedmiot',
        'icon' => 'case-sensitive',
    ],
];

$render_head = function (): string {
    return <<<HTML
    <link rel="stylesheet" href="/_css/view.css">
    HTML;
};

$render_content = function (): string {
    global $title, $listing, $listing_covers, $key_lookup_table, $iso;

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

    $attrib_list = json_decode($listing['attributes']) ?? null;

    if ($attrib_list) {
        foreach ($attrib_list as $attribute => $value) {
            $attribute = htmlspecialchars($attribute);
            $value = htmlspecialchars($value);

            switch ($attribute) {
                case 'language':
                    $language = $iso->languageByCode2t($value, true);
                    $native_language = $iso->nativeByCode2t($value, true);
                    $value = "{$native_language} ({$language})";
                    break;
            }

            $attributes .= <<<HTML
            <tr>
                <th class="with-icon"><i data-lucide="{$key_lookup_table[$attribute]['icon']}" aria-hidden="true"></i>{$key_lookup_table[$attribute]['display']}</th>
                <td>{$value}</td>
            </tr>
            HTML;
        }
    }

    $carousel_section = ($array_size === 0) ? null : <<<HTML
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

    $attribute_table = ($attributes === "") ? "" : <<<HTML
        <table id="details">
            <tbody>
               {$attributes}
            </tbody>
        </table>
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
                    {$attribute_table}
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
};

$render_scripts = function () {
    return <<<HTML
    <script src="/_js/carousel.js"></script>
    HTML;
};

require $_SERVER['DOCUMENT_ROOT'] . '/../resources/components/container.php';
