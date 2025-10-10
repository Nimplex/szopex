<?php
require $_SERVER['DOCUMENT_ROOT'] . '/../resources/check-auth.php';

if (isset($_SERVER['HTTP_HX_REQUEST'])) {
    require $_SERVER['DOCUMENT_ROOT'] . '/../resources/hx-templates/listings.php';
    die;
}
?>

<!DOCTYPE HTML>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/_css/listings.css">
    <script src="/_js/htmx.min.js"></script>
    <title>Oferty</title>
</head>
<body>
    <h1>Aktualne oferty</h1>
    <div id="listings-outer">
        <div id="offers">
            <?php require $_SERVER['DOCUMENT_ROOT'] . '/../resources/hx-templates/listings.php'; ?>
            <!-- this is just a placeholder, later we can put something else in here -->
            <span id="throbber" class="htmx-indicator">Loading...</span>
        </div>
    </div>
</body>

</html>
