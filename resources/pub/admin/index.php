<?php
$TITLE = 'Panel administratora';
$HEAD = '<link rel="stylesheet" href="/_dist/css/admin.css">';

ob_start();
?>

<?php require $_SERVER['DOCUMENT_ROOT'] . '/../resources/pub/admin/components/sidebar.php' ?>

<div id="content">
</div>

<?php
$CONTENT = ob_get_clean();

require $_SERVER['DOCUMENT_ROOT'] . '/../resources/components/container.php';
