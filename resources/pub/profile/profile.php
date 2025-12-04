<?php

/** @var \App\Controller\UserController $user_controller */
global $user_controller, $_ROUTE;

use App\Builder\ListingBuilder;
use App\Helper\DateHelper;

$listingModel = (new ListingBuilder())->make();

$id = filter_var($_ROUTE['id'] ?? null, FILTER_VALIDATE_INT);

// Just in case
if (!isset($id)) {
    header('Location: /', true, 303);
    die;
}

$res = $user_controller->user->get_profile($id);
if (!$res) {
    require $_SERVER['DOCUMENT_ROOT'] . '/../resources/errors/404.php';
}

$listings = $listingModel->listByUser($id);

$TITLE = 'Użytkownik ' . htmlspecialchars($res['display_name']);
$HEAD = '<link rel="stylesheet" href="/_dist/css/profile.css">';

$display_name = htmlspecialchars($res['display_name']);
$created_at = date('d.m.Y', strtotime($res['created_at']));
$listing_count = $res['listing_count'];
$listing_count_text = DateHelper::pluralize($listing_count, "ogłoszenie", "ogłoszenia", "ogłoszeń");
$picture_id = urlencode($res['picture_id']);

ob_start();
?>

<div id="profile-sidebar">
    <section id="profile-info">
        <img
            src="/api/storage/profile-pictures/<?= $picture_id ?>"
            id="profile-picture"
            alt="Zdjęcie profilowe użytkownika <?= $display_name ?>"
        >
        <div class="details">
            <h1><?= $display_name ?></h1>
            <div class="stat">
                <i data-lucide="calendar" aria-hidden="true"></i>
                Dołączył <?= $created_at ?>
            </div>
            <div class="stat">
                <i data-lucide="package" aria-hidden="true"></i>
                <?= $listing_count, $listing_count_text ?>
            </div>
        </div>
    </section>
    <section id="profile-edit">
        <?php if ($_SESSION['user_id'] == $id): ?>
        <button>Edytuj profil</button>
        <form action="/api/logout" method="GET">
            <button class="btn-red-alt" type="submit">
                <i class="flipped" data-lucide="log-out" aria-hidden="true"></i>
                <span>Wyloguj się</span>
            </button>
        </form>
        <?php else: ?>
        <form action="/messages" method="GET">
            <input type="hidden" name="new_chat" value="1">
            <input type="hidden" name="user_id" value="<?= $id ?>">
            <button type="submit" class="btn-accent">
                <i data-lucide="message-circle" aria-hidden="true"></i>
                <span>Napisz do użytkownika</span>
            </button>
        </form>
        <button class="btn-red-alt"><i data-lucide="flag" aria-hidden="true"></i>Zgłoś profil</button>
        <?php endif; ?>
    </section>
</div>
<section id="listings-section">
    <div id="heading">
        <h2>Ogłoszenia użytkownika</h2>
        <?php if ($_SESSION['user_id'] == $id): ?>
        <form action="/listings/new" method="GET">
            <button class="btn-accent" type="submit">
                <i data-lucide="package-plus" aria-hidden="true"></i>
                Nowe ogłoszenie
            </button>
        </form>
        <?php endif; ?>
    </div>
    <?php if (empty($listings)): ?>
    <div class="no-listings">
        <i class="big-icon" data-lucide="package-x" aria-hidden="true"></i>
        Ten użytkownik nie ma jeszcze żadnych ogłoszeń
    </div>
    <?php endif; ?>
    <?php foreach ($listings as $listing): ?>
    <a href="/listings/<?= $listing['listing_id'] ?>" class="listing-card">
        <?php if (!empty($listing['cover_file_id'])): ?>
        <img src="/api/storage/covers/<?= $listing['cover_file_id'] ?>" class="listing-cover" alt="<?= htmlspecialchars($listing['title']) ?>">
        <?php endif; ?>
        <article class="listing-info">
            <h3><?= htmlspecialchars($listing['title']) ?></h3>
            <div class="listing-meta">
                <span><i data-lucide="calendar" aria-hidden="true"></i><?= date('d.m.Y', strtotime($listing['created_at'])) ?></span>
                <span>
                    <i data-lucide="<?= $listing['active'] ? 'check-circle' : 'x-circle' ?>" aria-hidden="true"></i>
                    <?= $listing['active'] ? 'Aktywne' : 'Nieaktywne' ?>
                </span>
            </div>
            <div class="price"><?= $listing['price'] ?></div>
        </article>
    </a>
    <?php endforeach; ?>
</section>

<?php
$CONTENT = ob_get_clean();

require $_SERVER['DOCUMENT_ROOT'] . '/../resources/components/container.php';
