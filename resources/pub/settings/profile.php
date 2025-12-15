<?php

/** @var \App\Controller\UserController $user_controller */
global $user_controller;

$SETTINGS_PAGE = [
    'self-url' => '/settings/profile',
    'head' => '<link rel="stylesheet" href="/_dist/css/settings/profile.css">',
    'title' => 'Edytuj profil',
    'scripts' => [
        '/_dist/js/settings/profile.js',
        '/_dist/js/form_modified.js',
    ],
];

$profile = $user_controller->user->get_profile($_SESSION['user_id']);
if (!$profile) {
    require $_SERVER['DOCUMENT_ROOT'] . '/../resources/errors/404.php';
    die;
}

$edit_indicator = '<span class="edit-indicator" aria-hidden="true">&nbsp;•</span>';

ob_start();
?>

<form action="/api/update-profile" method="POST" enctype="multipart/form-data">
    <div class="row">
        <section id="set-pfp">
            <div class="check-updates">
                <span class="edit-indicator" aria-hidden="true">•</span>
                <label id="set-pfp-inner">
                    <input
                        type="file"
                        class="sr-only"
                        onchange="setPreview(this)"
                        name="pfp"
                        accept="image/jpeg,image/png"
                    >
                    <img
                        src="/api/storage/profile-pictures/<?= urlencode($profile['picture_id']) ?>"
                        id="profile-picture"
                        alt="Obecne zdjęcie profilowe"
                    >
                    <i data-lucide="pen" aria-hidden="true"></i>
                    <span class="sr-only">Zmień zdjęcie profilowe</span>
                </label>
            </div>
            Naciśnij, aby zmienić zdjęcie profilowe
        </section>
        <section id="user-name-values">
            <label class="check-updates">
                Nazwa wyświetlana:
                <?= $edit_indicator ?>
                <input type="text" name="user_name" value="<?= htmlspecialchars($profile['display_name']) ?>">
            </label>
            <br>
            <label class="check-updates">
                Opis:
                <?= $edit_indicator ?>
                <textarea type="text" name="user_description"><?= htmlspecialchars($profile['description']) ?></textarea>
            </label>
        </section>
    </div>
    <br>
    <div id="counter-wrapper">
        <button type="submit" class="btn-accent">
            <i data-lucide="save" aria-hidden="true"></i>
            <span>Zapisz zmiany</span>
        </button>
        <span id="update-counter"></span>
    </div>
</form>

<?php
$CONTENT = ob_get_clean();

require $_SERVER['DOCUMENT_ROOT'] . '/../resources/components/settings.php';
