<?php

global $_ROUTE;

use App\Builder\ListingBuilder;
use Matriphe\ISO639\ISO639;

$iso = new ISO639();
$listingBuilder = (new ListingBuilder())->make();

$listing_id = filter_var($_ROUTE['id'] ?? null, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);

// Just in case
if (!isset($listing_id)) {
    header('Location: /', true, 303);
    die;
}

$listing = $listingBuilder->get($listing_id, $_SESSION['user_id']);

if (!$listing) {
    require $_SERVER['DOCUMENT_ROOT'] . '/../resources/errors/404.php';
    die;
}

$listing_covers = $listingBuilder->getCovers($listing_id);

$TITLE = htmlspecialchars($listing['title']);
$HEAD = '<link rel="stylesheet" href="/_dist/css/view.css">';
$SCRIPTS = [
    '/_dist/js/listings.js',
    '/_dist/js/carousel.js',
    '/_dist/js/report.js',
];

$main_cover = "";
foreach ($listing_covers as $cover) {
    if ($cover['main']) {
        $main_cover = $cover['file_id'];
        break;
    }
}

$attrib_list = json_decode($listing['attributes']) ?? [];
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

ob_start();
?>

<div class="row">
    <?php if (!empty($listing_covers)): ?>
    <section class="carousel" role="region" aria-roledescription="carousel" aria-label="Zdjęcia oferty">
        <div id="cover-container">
            <img src="/api/storage/covers/<?= $main_cover ?>" id="main-cover">
            <?php $img_controls_disabled = (count($listing_covers) > 1) ? "" : " disabled" ?>
            <button class="left" aria-label="Poprzednie zdjęcie" tabindex="-1"<?= $img_controls_disabled ?>>‹</button>
            <button class="right" aria-label="Kolejne zdjęcie" tabindex="-1"<?= $img_controls_disabled ?>>›</button>
        </div>
        <hr>
        <ul>
            <?php foreach ($listing_covers as $cover): ?>
            <li>
                <img src="/api/storage/covers/<?= $cover['file_id'] ?>" alt="Podgląd <?= $TITLE ?>" tabindex="0">
            </li>
            <?php endforeach; ?>
        </ul>
    </section>
    <?php endif; ?>
    <div id="inner-container">
        <h1><?= $TITLE ?></h1>
        <hr>
        <div class="row">
            <section id="data-section">
                <div>
                    <p class="with-icon"><i data-lucide="book-open-text" aria-hidden="true"></i>Opis:</p>
                    <p><?= htmlspecialchars($listing['description']) ?></p>
                </div>
                <?php if (!empty($attrib_list)): ?>
                <table id="details">
                    <tbody>
                        <?php
                        foreach ($attrib_list as $attribute => $value):
                            $a = htmlspecialchars($attribute);
                            $v = ($a == "language")
                                ? sprintf("%s (%s)", $iso->languageByCode2t($value), $iso->nativeByCode2t($value, true))
                                : htmlspecialchars($value);
                            ?>
                            <tr>
                                <th class="with-icon">
                                    <i data-lucide="<?= $key_lookup_table[$attribute]['icon'] ?>" aria-hidden="true"></i>
                                    <?= $key_lookup_table[$attribute]['display'] ?>
                                </th>
                                <td><?= $v ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </section>
            <section id="button-section">
                <h1><?= htmlspecialchars($listing['price']) ?></h1>
                <?php $template_label = ($listing['is_favourited'] ?? null) ? "Usuń z ulubionych" : "Dodaj do ulubionych" ?>
                <button
                    type="button"
                    class="<?= ($listing['is_favourited'] ?? null) ? 'favourited' : '' ?>"
                    onclick="window.favourite(event)"
                    data-listing-id="<?= $listing_id ?>"
                    aria-label="<?= $template_label ?>"
                >
                    <i data-lucide="star" aria-hidden="true"></i>
                    <span><?= $template_label ?></span>
                </button>
                <?php if ($listing['user_id'] != $_SESSION['user_id']): ?>
                <form action="/messages" method="GET">
                    <input type="hidden" name="new_chat" value="1">
                    <input type="hidden" name="listing_id" value="<?= $listing_id ?>">
                    <button
                        type="submit"
                        class="btn-accent"
                        aria-label="Skontaktuj się z sprzedającym na temat '<?= $TITLE ?>'"
                    >
                        <i data-lucide="message-circle" aria-hidden="true"></i>
                        <span>Napisz do ogłoszeniodawcy</span>
                    </button>
                </form>
                <?php endif; ?>
                <button class="btn-red-alt" data-listing-id="<?= $listing['listing_id'] ?>" onclick="window.report(event)"><i data-lucide="flag"></i>Zgłoś</button>
            </section>
        </div>
    </div>
</div>

<?php
$CONTENT = ob_get_clean();

require $_SERVER['DOCUMENT_ROOT'] . '/../resources/components/container.php';
