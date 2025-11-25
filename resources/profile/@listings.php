<?php

$listingModel = (new App\Builder\ListingBuilder())->make();

$title = "Oferty użytkownika {$_SESSION['user_display_name']}";

$render_content = function () use ($listingModel) {
    $html = "<h1>Moje oferty</h1><div>";
    foreach ($listingModel->listByUser($_SESSION['user_id']) as $listing) {
        $html .= <<<HTML
        <div class="listing">
            <div class="listing-title">{$listing['title']}</div>
            <div class="listing-price">{$listing['price']}</div>
            <div class="listing-timestamp">{$listing['updated_at']}</div>
        </div>
        HTML;
    }
    $html .= "</div>";
    return $html;
};

require $_SERVER['DOCUMENT_ROOT'] . '/../resources/components/container.php';
