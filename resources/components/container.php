<?php

$lang = $_GET['lang'] ?? 'pl';
$allowed = ['pl', 'en'];
if (!in_array($lang, $allowed, true)) {
    $lang = 'en';
}

//
// NOTE: PhpActor will scream about undefined functions, you can safely ignore
// that. It is used by templates to render it's custom content.
//

/** @var string $title */
/** @var bool $no_navbar */
/** @var function $render_head */
/** @var function $render_content */
/** @var string $render_scripts */
?>

<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>">

<head>
    <meta charset="utf-8">
    <meta name="color-scheme" content="dark">
    <title><?= $title ?? 'Helix enthusiasts club' ?> | Szopex</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="preload" href="/_dist/css/base.css" as="style">

    <?php if (!isset($no_navbar)): ?>
        <link rel="preload" href="/_dist/js/navbar.js" as="script">
    <?php endif; ?>

    <link rel="preload" href="https://rsms.me/inter/inter.css" as="style">
    <link rel="preconnect" href="https://rsms.me/">
    <link rel="stylesheet" href="/_dist/css/base.css">
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    <link rel="icon" type="image/png" href="/_assets/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="/_assets/favicon.svg" />
    <link rel="shortcut icon" href="/_assets/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="/_assets/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="Szopex" />
    <link rel="manifest" href="/_assets/site.webmanifest" />
    <script type="module" src="/_dist/js/icons.js"></script>
    <?php if (isset($render_head)) {
        echo $render_head();
    } ?>
</head>

<body>
    <?php
    if (!isset($no_navbar)) {
        require $_SERVER['DOCUMENT_ROOT'] . '/../resources/components/navbar.php';
    } ?>
    <?php require $_SERVER['DOCUMENT_ROOT'] . '/../resources/components/message-box.php'; ?>

    <main>
    <?php if (isset($render_content)) {
        echo $render_content;
    } ?>
    </main>

    <?php if (isset($render_scripts)) {
        echo $render_scripts();
    } ?>

    <?php if (!isset($no_navbar)): ?>
        <script type="module" src="/_dist/js/navbar.js"></script>
    <?php endif; ?>

    <?php require $_SERVER['DOCUMENT_ROOT'] . '/../resources/components/footer.php'; ?>
</body>

</html>
