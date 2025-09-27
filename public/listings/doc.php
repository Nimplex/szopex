<?php
@session_start();
if (!isset($_SESSION['user_id'])) {
    require __DIR__ . '/../401.php';
    die;
}
?>
<!DOCTYPE HTML>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="/js/htmx.min.js"></script>
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
