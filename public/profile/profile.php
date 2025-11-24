<?php

session_start();

require $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';
require $_SERVER['DOCUMENT_ROOT'] . '/../resources/check-auth.php';

use App\Builder\AuthBuilder;
use App\Builder\ListingBuilder;

$user = (new AuthBuilder())->make();
$listingModel = (new ListingBuilder())->make();

$id = $_GET['id'];

$res = $user->user->get_profile($id);
$listings = $listingModel->listByUser($id);

$title = "Użytkownik {$res['display_name']}";

$render_head = function (): string {
    return <<<HTML
    <link rel="stylesheet" href="/_css/profile.css">
    HTML;
};

$render_content = function () use ($res, $listings) {
    [
        'display_name' => $display_name,
        'created_at' => $created_at,
        'listing_count' => $listing_count,
        'picture_id' => $picture_id,
    ] = $res;

    $display_name = htmlspecialchars($display_name);
    $created_at_formatted = date('d.m.Y', strtotime($created_at));
    
    $img_element = "";
    if (isset($picture_id)) {
        $picture_id_encoded = urlencode($picture_id);
        $img_element = <<<HTML
        <img src="/profile-picture.php?file={$picture_id_encoded}" id="profile-picture" alt="Zdjęcie profilowe {$display_name}">
        HTML;
    }

    $listings_html = "";
    if (empty($listings)) {
        $listings_html = <<<HTML
        <div class="no-listings">
            <i data-lucide="inbox" aria-hidden="true"></i>
            Ten użytkownik nie ma jeszcze żadnych ogłoszeń
        </div>
        HTML;
    } else {
        foreach ($listings as $listing) {
            $listing_id = urlencode($listing['id']);
            $listing_title = htmlspecialchars($listing['title']);
            $listing_price = htmlspecialchars($listing['price']);
            $listing_date = date('d.m.Y', strtotime($listing['created_at']));
            $listing_active = $listing['active'] ? 'Aktywne' : 'Nieaktywne';
            $listing_status_icon = $listing['active'] ? 'check-circle' : 'x-circle';
            
            $cover_html = "";
            if (!empty($listing['cover_file_id'])) {
                $cover_id = urlencode($listing['cover_file_id']);
                $cover_html = <<<HTML
                <img src="/covers.php?file={$cover_id}" class="listing-cover" alt="{$listing_title}">
                HTML;
            }

            $listings_html .= <<<HTML
            <a href="/listings/view.php?listing={$listing_id}" class="listing-card">
                {$cover_html}
                <div class="listing-info">
                    <h3>{$listing_title}</h3>
                    <div class="listing-meta">
                        <span><i data-lucide="calendar" aria-hidden="true"></i>{$listing_date}</span>
                        <span><i data-lucide="{$listing_status_icon}" aria-hidden="true"></i>{$listing_active}</span>
                    </div>
                    <div class="price">{$listing_price}</div>
                </div>
            </a>
            HTML;
        }
    }

    return <<<HTML
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
                {$listing_count} ogłoszeń
            </div>
        </div>
    </section>
    <section id="listings-section">
        <h2>Ogłoszenia użytkownika</h2>
        {$listings_html}
    </section>
    HTML;
};

require $_SERVER['DOCUMENT_ROOT'] . '/../resources/components/container.php';
