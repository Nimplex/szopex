<?php
require $_SERVER['DOCUMENT_ROOT'] . '/../resources/check-auth.php';

$_target = "/listings/my-listings.php";
require $_SERVER['DOCUMENT_ROOT'] . '/../resources/hx-index.php';

// ==========================

require $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';
$listingModel = (new App\Builder\ListingBuilder())->make();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Moje oferty</title>
</head>
<body>
    <h1>Moje oferty</h1>
    <div>
        <?php foreach ($listingModel->listByUser($_SESSION['user_id']) as $listing): ?>
        <div class="listing">
            <div class="listing-title">
                <?= $listing['title'] ?>
            </div>
            <div class="listing-price">
                <?= $listing['price'] ?>
            </div>
            <div class="listing-timestamp">
                <?= $listing['updated_at'] ?>
            </div>
            <br>
        </div>
        <?php endforeach; ?>
    </div>
</body>

</html>
