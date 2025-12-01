<?php

/** @var \App\Controller\UserController $user */
global $user, $_ROUTE;

use App\Builder\ListingBuilder;
use App\Helper\DateHelper;

$listingModel = (new ListingBuilder())->make();

$id = $_ROUTE['id'];

$res = $user->user->get_profile($id);
if (!$res) {
    require $_SERVER['DOCUMENT_ROOT'] . '/../resources/errors/404.php';
}

$listings = $listingModel->listByUser($id);

$title = "Użytkownik {$res['display_name']}";

$render_head = function (): string {
    return <<<HTML
    <link rel="stylesheet" href="/_dist/css/profile.css">
    HTML;
};

$render_content = function () use ($res, $listings, $id) {
    [
        'display_name' => $display_name,
        'created_at' => $created_at,
        'listing_count' => $listing_count,
        'picture_id' => $picture_id,
    ] = $res;

    $listing_count_text = DateHelper::pluralize($listing_count, "ogłoszenie", "ogłoszenia", "ogłoszeń");

    $display_name = htmlspecialchars($display_name);
    $created_at_formatted = date('d.m.Y', strtotime($created_at));
    
    $picture_id_encoded = urlencode($picture_id);
    $img_element = <<<HTML
    <img src="/api/storage/profile-pictures/{$picture_id_encoded}" id="profile-picture" alt="Zdjęcie profilowe użytkownika {$display_name}">
    HTML;

    $listings_html = "";
    if (empty($listings)) {
        $listings_html = <<<HTML
        <div class="no-listings">
            <i class="big-icon" data-lucide="package-x" aria-hidden="true"></i>
            Ten użytkownik nie ma jeszcze żadnych ogłoszeń
        </div>
        HTML;
    } else {
        foreach ($listings as $listing) {
            $listing_id = urlencode($listing['listing_id']);
            $listing_title = htmlspecialchars($listing['title']);
            $listing_price = htmlspecialchars($listing['price']);
            $listing_date = date('d.m.Y', strtotime($listing['created_at']));
            $listing_active = $listing['active'] ? 'Aktywne' : 'Nieaktywne';
            $listing_status_icon = $listing['active'] ? 'check-circle' : 'x-circle';
            
            $cover_html = "";

            if (!empty($listing['cover_file_id'])) {
                $cover_id = urlencode($listing['cover_file_id']);
                $cover_html = <<<HTML
                <img src="/api/storage/covers/{$cover_id}" class="listing-cover" alt="{$listing_title}">
                HTML;
            }

            $listings_html .= <<<HTML
            <a href="/listings/{$listing_id}" class="listing-card">
                {$cover_html}
                <article class="listing-info">
                    <h3>{$listing_title}</h3>
                    <div class="listing-meta">
                        <span><i data-lucide="calendar" aria-hidden="true"></i>{$listing_date}</span>
                        <span><i data-lucide="{$listing_status_icon}" aria-hidden="true"></i>{$listing_active}</span>
                    </div>
                    <div class="price">{$listing_price}</div>
                </article>
            </a>
            HTML;
        }
    }

    $profile_edit_section = ($_SESSION['user_id'] == $id) ? <<<HTML
    <button>Edytuj profil</button>
    <form action="/api/logout" method="get">
        <button class="btn-red-alt" type="submit">
            <i class="flipped" data-lucide="log-out" aria-hidden="true"></i>
            <span>Wyloguj się</span>
        </button>
    </form>
    HTML : <<<HTML
    <form action="/messages" method="get">
        <input type="hidden" name="new_message" value="t">
        <input type="hidden" name="user_id" value="{$id}">
        <button type="submit" class="btn-accent">
            <i data-lucide="message-circle" aria-hidden="true"></i>
            <span>Napisz do użytkownika</span>
        </button>
    </form>
    <button class="btn-red-alt"><i data-lucide="flag" aria-hidden="true"></i>Zgłoś profil</button>
    HTML;

    $new_listing_button = ($_SESSION['user_id'] == $id) ? <<<HTML
    <form action="/listings/new" method="GET">
        <button class="btn-accent" type="submit">
            <i data-lucide="package-plus" aria-hidden="true"></i>
            Nowe ogłoszenie
        </button>
    </form>
    HTML : "";

    return <<<HTML
    <div id="profile-sidebar">
        <section id="profile-info">
            {$img_element}
            <div class="details">
                <h1>{$display_name}</h1>
                <div class="stat">
                    <i data-lucide="calendar" aria-hidden="true"></i>
                    Dołączył {$created_at_formatted}
                </div>
                <div class="stat">
                    <i data-lucide="package" aria-hidden="true"></i>
                    {$listing_count} {$listing_count_text}
                </div>
            </div>
        </section>
        <section id="profile-edit">
            {$profile_edit_section}
        </section>
    </div>
    <section id="listings-section">
        <div id="heading">
            <h2>Ogłoszenia użytkownika</h2>
            {$new_listing_button}
        </div>
        {$listings_html}
    </section>
    HTML;
};

require $_SERVER['DOCUMENT_ROOT'] . '/../resources/components/container.php';
