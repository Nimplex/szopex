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
/** @var function $render_scripts */
?>

<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>">

<head>
    <meta charset="utf-8">
    <meta name="color-scheme" content="dark">
    <title><?= $title ?? 'Helix enthusiasts club' ?> | Szopex</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="preload" href="/_dist/css/base.css" as="style">
    <link rel="preload" href="/_dist/js/navbar.js" as="script">
    <link rel="preload" href="https://rsms.me/inter/inter.css" as="style">
    <link rel="preconnect" href="https://rsms.me/">
    <link rel="stylesheet" href="/_dist/css/base.css">
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
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
        echo $render_content();
    } ?>
    </main>

    <?php if (isset($render_scripts)) {
        echo $render_scripts();
    } ?>

    <script type="module" src="/_dist/js/navbar.js"></script>

    <?php require $_SERVER['DOCUMENT_ROOT'] . '/../resources/components/footer.php'; ?>
</body>

</html>
