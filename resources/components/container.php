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

?>

<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>">

<head>
    <meta charset="utf-8">
    <meta name="color-scheme" content="dark" />
    <title><?= $title ?? 'Neovim enjoyers club'?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="preload" href="/_css/base.css" as="style">
    <link rel="preload" href="/_js/htmx.min.js" as="script">
    <link rel="stylesheet" href="/_css/base.css">
    <link rel="preconnect" href="https://rsms.me/">
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    <script src="https://unpkg.com/lucide@latest"></script>
    <?php if (function_exists('render_head')) {
        echo render_head();
    } ?>
</head>

<body>
    <?php
    if (!isset($no_navbar)) {
        require $_SERVER['DOCUMENT_ROOT'] . '/../resources/components/navbar.php';
    } ?>
    <?php require $_SERVER['DOCUMENT_ROOT'] . '/../resources/hx-templates/message-box.php'; ?>

    <main>
    <?php if (function_exists('render_content')) {
        echo render_content();
    } ?>
    </main>

    <?php if (function_exists('render_scripts')) {
        echo render_scripts();
    } ?>

    <script>
        lucide.createIcons();
    </script>

    <?php require $_SERVER['DOCUMENT_ROOT'] . '/../resources/components/footer.php'; ?>
</body>

</html>
