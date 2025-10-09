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
    <script src="/_js/htmx.min.js"></script>
    <title>Oferty</title>
</head>
<body>
    <h1>Aktualne oferty</h1>
    <div id="listings-outer">
        <div id="aei"></div>
        <button hx-get="?page=<?= max($_GET['page'] ?: 1, 1) ?>" hx-target="#aei">ae</button>
    </div>
</body>

</html>
