<?php


/** @var \App\Controller\UserController $user_controller */
global $user_controller;

$users = $user_controller->user->admin_get_all();

$SETTINGS_PAGE = [
    'self-url' => '/admin/users',
    'head' => <<<HTML
    <style>
    table {
        margin-top: 1rem;
    }
    table, tr, th, td {
        border: 1px solid var(--border-gray);
    }

    th, td {
        padding: 0.5rem;
        text-align:left;
    }
    </style>
    HTML,
     'title' => 'Użytkownicy',
    'scripts' => ''
];

ob_start();
?>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Login</th>
            <th>Nazwa wyświetlana</th>
            <th>E-mail</th>
            <th>Poziom</th>
            <th>Data utworzenia</th>
            <th>Ilość ogłoszeń</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($users as $user): ?>
        <tr>
            <td><?= $user['id'] ?></td>
            <td><?= $user_controller->has_permissions('administrator') ? $user['login'] : '---hidden---' ?></td>
            <td><?= $user['display_name'] ?></td>
            <td><?= $user_controller->has_permissions('superadministrator') ? $user['email'] : '---hidden---' ?></td>
            <td><?= $user['role'] ?? 'użytkownik' ?></td>
            <td><?= $user['created_at'] ?></td>
            <td><?= $user['listing_count'] ?></td>
        </tr>
    <?php endforeach ?>
    </tbody>
</table>

<?php
$CONTENT = ob_get_clean();

require $_SERVER['DOCUMENT_ROOT'] . '/../resources/components/admin.php';
