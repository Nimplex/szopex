<?php
$SETTINGS_PAGE = [
    'self-url' => '/admin',
    'head' => '',
    'title' => 'Strona główna',
    'scripts' => ''
];

ob_start();
?>

<?php
$CONTENT = ob_get_clean();

require $_SERVER['DOCUMENT_ROOT'] . '/../resources/components/admin.php';
