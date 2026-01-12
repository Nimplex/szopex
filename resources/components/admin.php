<?php
/** @var array<string,mixed> $SETTINGS_PAGE */
/** @var string $CONTENT */

$title = 'Administrator';

$TITLE = sprintf('%s: %s', $title, $SETTINGS_PAGE['title']);
$HEAD = <<<HTML
<link rel="stylesheet" href="/_dist/css/settings/main.css">
<link rel="stylesheet" href="/_dist/css/sidebar.css">
HTML . ($SETTINGS_PAGE['head'] ?? '');

$SCRIPTS = [
    '/_dist/js/sidebar.js',
    ...$SETTINGS_PAGE['scripts'] ?? []
];

$SIDEBAR_CFG = [
    'type' => 'link',
    'title' => $title,
    'groups' => [
        [
            ['Strona główna', '/admin', 'house'],
        ],
        [
            ['Zgłoszenia', '/admin/reports', 'flag'],
        ],
        [
            ['Użytkownicy', '/admin/users', 'user'],
        ],
    ],
    'group_names' => [
        'Ogólne',
        'Zgłoszenia',
        'Zarządzanie'
    ],
    'selected' => $SETTINGS_PAGE['self-url'],
];

ob_start();
?>

<div id="sidebar-wrapper">
    <?php require $_SERVER['DOCUMENT_ROOT'] . '/../resources/components/sidebar.php' ?>
    <section id="sidebar-pane">
        <div id="heading">
            <button id="sidebar-open-button" type="button" onclick="window.openSidebar();">
                <i data-lucide="menu" aria-hidden="true"></i>
                <span class="sr-only">Otwórz panel boczny</span>
            </button>
            <h1><?= $SETTINGS_PAGE['title'] ?></h1>
        </div>
        <hr>
        <?= $CONTENT ?>
    </section>
</div>
<?php
$CONTENT = ob_get_clean();

require $_SERVER['DOCUMENT_ROOT'] . '/../resources/components/container.php';
